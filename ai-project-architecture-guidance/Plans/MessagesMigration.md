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

**STATUS (updated 2026-05-28): all four workstreams below are COMPLETE,
committed, and verified in dev.** The next phase is the front-end cutover —
see "Front-end cutover — verified contract & step-by-step" near the end of this
doc, which is the authoritative, code-verified handoff. The workstream
descriptions that follow are kept for historical context.

Generator is finished and lives as small role-based collaborators in
`api/src/Messages/Generate/` (`GenerateMessagesPagesForRedis` composer plus
`MessagesPagesBuilder`, `BySpeakerPagesBuilder`, `BySeriesPagesBuilder`,
`MostRecentSeriesBuilder`, `BySpeakerOptionsBuilder`, `BySeriesOptionsBuilder`,
`MessageEntryJsonFactory`, key shapes centralized in `MessagesRedisKey`). It is
wired into a 5-minute schedule and message/series/profile CUD events, and the
search endpoint lives in `api/src/Messages/Search/`. All keys use the
`api-messages:` prefix.

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

## Front-end cutover — verified contract & step-by-step

API side (workstreams 1–4) is complete, committed, and verified in dev. This
section is now **in scope for local-dev testing**. It was rewritten from the
committed code (not memory) on 2026-05-28, so the file/line references and the
JSON contract below are exact. Removing Craft (steps 4 and 6) stays deferred
until the front-end is verified against the API in dev.

### The integration contract (already satisfied by the API)

**JSON envelope** — the Redis page payloads (`MessagesPagesBuilder`,
`BySpeakerPagesBuilder`, `BySeriesPagesBuilder`) and the search action
(`GetMessagesSearchAction`) all serialize the *same* envelope, which already
matches `web/app/audio/MessagesPageData.ts` exactly:

```
currentPage, perPage, totalResults, totalPages, pagesArray,
prevPageLink, nextPageLink, firstPageLink, lastPageLink, entries[]
```

- By-speaker pages add top-level `byName` + `bySlug` → matches `ByReturnType` in `FindAllMessagesBySpeakerByPage.ts`.
- By-series pages add top-level `seriesName` + `seriesSlug` → matches `series/[slug]` page usage.
- Single-message slug keys wrap the entry as `{ "entry": { … } }`.

**Entry shape** (`MessageEntryJsonFactory::create`, `web/app/audio/Entry.ts`):
`uid, title, slug, postDate, postDateDisplay, by{title,slug}|null, text,
series{title,slug}|null, audioFileName, audioFileSize`. `by` and `series` are
`null` when the speaker/series is the empty sentinel.

Conclusion: **no front-end type changes are required.** The cutover is a
key-prefix swap plus one base-URL swap.

### Redis key map (Craft prefix → API prefix)

The API writes the `api-messages:` namespace (see `MessagesRedisKey`). Flip the
prefix in each of these 7 repository files under
`web/app/media/messages/repository/`:

| File | Craft key | API key |
|------|-----------|---------|
| `FindAllMessagesByPage.ts` | `messages:page:{n}` | `api-messages:page:{n}` |
| `FindMessageBySlug.ts` | `messages:slug:{slug}` | `api-messages:slug:{slug}` |
| `FindAllMessagesBySpeakerByPage.ts` | `messages:by:{slug}:{n}` | `api-messages:by:{slug}:{n}` |
| `FindAllMessagesBySeriesByPage.ts` | `messages:series:{slug}:{n}` | `api-messages:series:{slug}:{n}` |
| `FindAllSeriesOptions.ts` | `messages:series_options` | `api-messages:series_options` |
| `FindAllByOptions.ts` | `messages:by_options` | `api-messages:by_options` |
| `FindRecentSeries.ts` | `messages:most_recent_series` | `api-messages:most_recent_series` |

Cleanup while there: `FindAllMessagesBySpeakerByPage.ts` lines 16–18 hold a dead
`const tmp = await redis.keys('messages:by:*')` — delete it.

### Search endpoint cutover (1 file)

`web/app/media/messages/repository/SearchMessagesByPage.ts` line 18 builds the
URL from `ConfigOptions.APP_API_URL`. In `docker/web/.env`:

- `APP_API_URL=http://stmark-app` → the **Craft** app (current search backend).
- `API_URL=http://stmark-api` → the **new PHP API** (already present, already serves `/api/media/messages/search`).

So the cutover is: change `ConfigOptions.APP_API_URL` → `ConfigOptions.API_URL`
on that line. **No new env var** (the plan's earlier `INTERNAL_API_URL` idea is
unnecessary). The query-param names already match `SearchMessagesParams`
(`by[]`, `series[]`, `scripture_reference`, `title`, `date_range_start`,
`date_range_end`, `page`).

### Must-verify before trusting the cutover

1. **Container identity** — confirm in `docker/docker-compose.dev.yml` that `stmark-api` is the new PHP API service and `stmark-app` is Craft. The whole search swap hinges on this.
2. **`api-messages:*` is populated** — `redis-cli KEYS 'api-messages:*'` should be non-empty. If not, run the generator command (`messages:generate-redis-pages`, registered via `GenerateMessagesPagesForRedisCommand`) and/or confirm the 5-min schedule is firing.
3. **Audio files — leave the proxy alone.** `docker/proxy/default.conf.template` line 62 routes `location /uploads` → `${CRAFT_PROXY}`, and it stays that way. Audio playback uses `audioFileName` under `/uploads/audio/…`; TJ is handling audio serving via Docker volume manipulation on the front-end side, so the cutover does **not** touch the proxy template and does not depend on the API serving `/uploads`. The page/search JSON works regardless of where the audio bytes come from.

### Suggested cutover order (local dev)

1. Verify items 1–2 above (container names + populated keys).
2. Flip the 7 Redis-prefix files. Reload listing/by/series/slug pages; compare against current (Craft-served) pages — they should be byte-identical since both read the same envelope shape.
3. Flip `SearchMessagesByPage.ts` to `API_URL`. Exercise every search filter combination and compare against Craft's search results.
4. `tsc` + `eslint` clean.

Do **not** touch `docker/proxy/default.conf.template` — audio serving is being
handled separately via Docker volume manipulation (see must-verify item 3).

### Still deferred (do NOT do during local-dev testing)

- **Stop Craft's messages schedule + CUD subscribers** (so Craft stops writing the `messages:*` keys). Only after the front-end is verified on the API — keeping both running in parallel is what lets us compare.
- **Delete dead Craft messages code** (`craft-cms/src/Http/Response/Media/Messages/*` except anything still serving Craft's admin UI) and drop `APP_API_URL` if nothing else uses it.
- **Prod env / prod proxy changes.**

These are listed so we know where the cutover is pointed; they require explicit authorization.

## Execution order

Suggested sequence (each step is independently shippable):

1. **Workstream 1** — audio size on the table. Self-contained DB + persistence change.
2. **Workstream 2** — refactor and finish the generator. Depends on (1) for real `audioFileSize` values.
3. **Workstream 4** — search endpoint. Independent of (2) but shares the entry-JSON factory, so do it after (2) to reuse rather than fork.
4. **Workstream 3** — wire into schedule + CUD events. Last, because we only want the scheduled and event-triggered regeneration once the output is correct.

Front-end cutover happens after all four are merged and verified in dev.
