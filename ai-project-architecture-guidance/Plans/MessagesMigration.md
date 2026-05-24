# Messages Migration — Finish Strangler-Fig

## Goal

Get the messages (sermons) feature fully producible from the new API so that the front-end can later be flipped off Craft's Redis keys / search endpoint without losing functionality. This plan is scoped to the **API side** only. Front-end cutover is sketched at a high level at the end but is not part of execution yet.

## Why this is needed

The Next.js front-end at `web/` reads pre-built JSON blobs from Redis (`messages:page:*`, `messages:slug:*`, `messages:by:*`, `messages:series:*`, plus a few options keys) that **Craft** currently regenerates on a schedule and on entry events. Search is also still served by Craft at `/api/media/messages/search`. Until the API produces the same Redis shape and serves the same search endpoint, Craft cannot be removed from the runtime.

## Current state snapshot

What's already done on the API side:

- DB schema, repository, admin UI, and create/update/delete flows for messages, series, and profiles.
- Profile slugs (column + `Profile::$slug` + `profiles:resave-all` command).
- Series carries a `SeriesSlug` value object.
- Elasticsearch indexing: `IndexAllMessages` runs every 5 min via `ScheduleFactory`, and `CreateMessage` / update / delete call `EnqueueIndexAllMessages` so per-event re-indexing happens too.
- Import-from-Craft transfer commands have been run, so the API DB is hydrated.

Work in progress (uncommitted at the time of writing):

- `api/src/Messages/Generate/GenerateMessagesPagesForRedis.php` — half-done API-side port of Craft's [GenerateMessagesPagesForRedis.php](../../craft-cms/src/Http/Response/Media/Messages/GenerateMessagesPagesForRedis.php).
- `api/src/Pagination/Pagination.php` + `QueryString.php` — copied over to support the generator.
- `api/src/Messages/Messages.php` — adds `count`, `slice`, `sliceToPage`, `first`, `filter` to support paging.

Known issues in the WIP generator:

- Writes `api-messages:page:*` and `api-messages:slug:*` (correct: we want the prefix during dev to avoid colliding with Craft), but `generateByPage` writes `messages:by:*` without the prefix — bug.
- Hard-coded `'TODO'` / `'slug-todo'` for `by.slug`, `bySlug`, and `audioFileSize = 0`.
- Stray `dd()` debug calls left in `generate()` and `generateByPages()`.
- Doesn't yet implement: series pages, `most_recent_series`, `by_options`, `series_options`.
- Not wired into routes, the scheduler, or message CUD events.

## Design decisions (locked in)

- **Redis keys**: keep the `api-messages:` prefix throughout. Front-end will switch at cutover (out of scope here).
- **audioFileSize**: store on the `messages` table. Matches what Craft does (Craft reads `asset.size` from its DB, not the filesystem). Schema migration + `PersistMessageAudioFile` records size on save + backfill command for existing rows.
- **Search**: build `/api/media/messages/search` on the API, matching Craft's request/response shape exactly. Front-end cutover later flips a new env var.
- **Architecture**: per `AGENTS.md`, the generator should not stay as one big class. Break it into co-located, role-based workflow objects in `api/src/Messages/Generate/`.

## Workstreams

### 1. Persist `audioFileSize` on the message

Locality: `api/src/Messages/`.

- Migration: add `audio_file_size INT UNSIGNED NULL` to the `messages` table.
- `MessageRecord` (`Persistence/MessageRecord.php`): add `audio_file_size`.
- `Persistence/Transformer.php`: hydrate `Message::$audioFileSize` from the record.
- `Message.php` / `NewMessage.php`: add `int $audioFileSize = 0` constructor arg and surface in `asArray()` / docblock.
- `PersistMessageAudioFile::persist()`: after writing the file, `filesize()` the result and pass the value through to the persistence path. Two options here, recommend the second:
  - Return the size from `PersistMessageAudioFile` and have the caller re-persist the record with the size. Awkward because create/update both already wrote the record.
  - **Recommended:** restructure so audio metadata (filename + size) is captured before the row is persisted, and the row write includes it. If the file write fails after the row write, the existing rollback already handles it.
- Backfill: add a `messages:backfill-audio-sizes` CLI command. Iterates `findAll()`, stats `/var/www/public/uploads/audio/{audioPath}`, writes the size. Idempotent.

Open question to resolve when starting this step: whether `audioPath` already holds just the filename or includes a prefix. Looks like just the filename based on `PersistMessageAudioFile`, but verify.

### 2. Finish the Redis generator

Locality: `api/src/Messages/Generate/`.

Refactor the single WIP class into small named collaborators rather than growing it. Proposed shape (names are role-based, not "X-er" buckets):

- `GenerateMessagesPagesForRedis` — top-level composer. Loads `Messages` once, drives each sub-generator, performs the orphan-key cleanup at the end.
- `MessagesPageWriter` — writes `api-messages:page:N` and `api-messages:slug:{slug}` for the all-messages listing.
- `BySpeakerPagesWriter` — writes `api-messages:by:{speakerSlug}:N` per profile that has messages.
- `BySeriesPagesWriter` — writes `api-messages:series:{seriesSlug}:N` per series that has messages.
- `MostRecentSeriesWriter` — writes `api-messages:most_recent_series`.
- `BySpeakerOptionsWriter` — writes `api-messages:by_options` (grouped leadership / others).
- `BySeriesOptionsWriter` — writes `api-messages:series_options`.
- `MessageEntryJsonFactory` — produces the shared entry-array shape used by every writer (`uid`, `title`, `slug`, `postDate`, `postDateDisplay`, `by`, `text`, `series`, `audioFileName`, `audioFileSize`). Lives next to the writers, not in a generic helpers bucket.

Concrete fixes to bake in:

- Drop the prefix inconsistency. Every key starts with `api-messages:`.
- `by.slug` → `$message->speaker->slug` (Profile has it now).
- `bySlug` / `seriesSlug` in the by/series page payloads → real slugs (not `'TODO'`).
- `audioFileSize` → `$message->audioFileSize` (from workstream 1).
- Remove all `dd()` calls.
- Orphan cleanup must use the new prefix consistently: `api-messages:page:*`, `api-messages:slug:*`, `api-messages:by:{slug}:*`, `api-messages:series:{slug}:*`.
- Sort messages newest-first in `MessagesRepository::findAll()` (verify it already does; the all-messages page payload must be in display order).

Verify the entry shape matches Craft's exactly by diffing against [Craft's generator](../../craft-cms/src/Http/Response/Media/Messages/GenerateMessagesPagesForRedis.php) field-by-field. Keep the keys identical so the front-end repository code can swap key prefixes only.

### 3. Wire the generator into the runtime

Locality: `api/src/Messages/Generate/`, `api/config/`.

Follow the same pattern as `EnqueueIndexAllMessages`:

- Add `EnqueueGenerateMessagesPagesForRedis` (queue job enqueue-er) and a `GenerateMessagesPagesForRedisQueueJob` (queue job) — same shape Craft already has on its side.
- Schedule: add a `ScheduleItem` for `EnqueueGenerateMessagesPagesForRedis` (5-minute cadence to match the Craft side and `IndexAllMessages`) in `ScheduleFactory`.
- Per-event regeneration: in `CreateMessage`, `UpdateMessage` (whatever the update workflow is — see `PostEditMessageAction` chain), and `DeleteMessage`, call `$enqueueGenerateMessagesPagesForRedis->enqueue()` next to the existing `enqueueIndexAllMessages->enqueue()` call. Same for series and profile mutations (a renamed profile/series invalidates Redis keys).
- DI bindings: add to whichever `Dependencies/` file is appropriate (none of the current ones look obviously right — likely no binding needed since the auto-wiring container picks up readonly constructor injection by class name).

### 4. Search endpoint on the API

Locality: `api/src/Messages/Search/` (the existing search slice).

- Add `App\Messages\Search\SearchMessagesParams` mirroring Craft's [Params.php](../../craft-cms/src/Http/Response/Media/Messages/Params.php) — `page`, `by[]`, `series[]`, `scripture_reference`, `title`, `date_range_start`, `date_range_end`, `perPage = 25`. Typed object, not a raw array.
- Add `App\Messages\Search\SearchMessages` — runs the Elasticsearch query against `MessagesSearchIndex::MESSAGES`. Returns a `Messages` collection plus a total count.
- Add `App\Messages\Search\GetMessagesSearchAction` at `GET /api/media/messages/search`. Register in `ApplyRoutes`. Builds `Pagination`, calls `SearchMessages`, maps to the entry-JSON shape via the same `MessageEntryJsonFactory` from workstream 2, returns the same JSON envelope Craft returns (so the front-end's `MessagesPageData` type is unchanged).

This explicitly reuses the entry-shape factory from workstream 2 — search results and Redis listing results must serialize identically so the front-end doesn't have to branch.

### 5. Verification

For each workstream:

- Run the generator command (add a `messages:generate-redis-pages` CLI command for manual triggering) and inspect Redis with `redis-cli KEYS 'api-messages:*'`. Cross-check the JSON in `api-messages:page:1` against `messages:page:1` (Craft's) and confirm shape parity.
- Trigger the schedule runner once; confirm the keys regenerate.
- Create, edit, delete a message in the admin UI; confirm `api-messages:*` keys regenerate (and orphans are cleaned).
- Hit `/api/media/messages/search` (via API container directly: `curl http://localhost:.../api/media/messages/search?title=...`) with each combination of query params. Diff JSON against Craft's response.

No automated tests are mandatory for this WIP code because the existing project doesn't yet have generator tests, but consider unit-testing `MessageEntryJsonFactory` since multiple writers and the search action depend on its output shape.

## High-level front-end cutover (out of scope; for later)

When the API side is verified and parked behind the `api-messages:` keys + `/api/media/messages/search` on `stmark-api`:

1. New env var, e.g. `INTERNAL_API_URL=http://stmark-api`, added to `docker/web/.env` (and prod equivalent). Existing `APP_API_URL` stays (or is renamed) so nothing breaks during the swap.
2. Update `web/app/media/messages/repository/FindAllMessagesByPage.ts` and siblings to read `api-messages:*` keys.
3. Update `web/app/media/messages/repository/SearchMessagesByPage.ts` to call `${INTERNAL_API_URL}/api/media/messages/search`.
4. Stop Craft's schedule entry for `EnqueueGenerateMessagesPagesForRedis` and its CUD event subscribers.
5. Repoint `/uploads/` in `docker/proxy/default.conf.template` away from Craft. The shared volume already lives in both containers, so this is a proxy-only change.
6. Delete the now-dead Craft messages code (`craft-cms/src/Http/Response/Media/Messages/*` except whatever still serves Craft's admin UI), drop `APP_API_URL` if nothing else uses it.

That step is not authorized yet — listed only so we know where workstreams 1–4 are pointed.

## Execution order

Suggested sequence (each step is independently shippable):

1. **Workstream 1** — audio size on the table. Self-contained DB + persistence change.
2. **Workstream 2** — refactor and finish the generator. Depends on (1) for real `audioFileSize` values.
3. **Workstream 4** — search endpoint. Independent of (2) but shares the entry-JSON factory, so do it after (2) to reuse rather than fork.
4. **Workstream 3** — wire into schedule + CUD events. Last, because we only want the scheduled and event-triggered regeneration once the output is correct.

Front-end cutover happens after all four are merged and verified in dev.
