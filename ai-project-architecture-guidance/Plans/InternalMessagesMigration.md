# Internal Messages Migration — Strangler-Fig (backend side)

## Goal

Make the member-only "internal messages" (internal media) feature fully
producible from the new PHP API, so Craft can be removed from the runtime
without losing functionality. This mirrors what was already done for public
messages (see [MessagesMigration.md](./MessagesMigration.md)).

**This is a strangler-fig migration, exactly like public messages.** The API
writes to a **new, separate `api-`-prefixed Redis key namespace** and runs in
parallel with Craft. Craft keeps powering the live front-end on its existing
keys the entire time, so the API can be built and deployed bit by bit with zero
user-facing risk. Only at the end — as a small, deferred, reversible cutover —
does the front-end flip to consume the new prefixed keys.

This plan covers the API + Craft-transfer side (the writer) plus the front-end
cutover (a key-prefix flip), kept as a deferred final step.

## Why this is needed

The Next.js front-end at `web/app/members/` is fully built: member login, the
internal-media listing / detail / by-speaker / by-series pages, and a
member-gated audio streaming route. All of it reads pre-built JSON blobs from
Redis (`members:internal_media:page:*`, `:slug:*`, `:by:*`, `:series:*`) and
streams audio from the `files-above-webroot-volume`.

Today **Craft** is the thing that:

- regenerates those Redis blobs (on a schedule and on entry events) via
  `craft-cms/src/Http/Response/Members/InternalMedia/GenerateInternalMediaPagesForRedis.php`, and
- stores the uploaded internal audio into `filesAboveWebroot/internal-audio/{slug}/{filename}`.

Until the API produces the same Redis shape (under its own prefix) and writes
audio into the same volume, Craft cannot be removed.

## How this mirrors the public messages migration

Same strangler-fig shape as public messages:

- The API writes a **new key namespace** (`api-members:internal_media:*`) while
  Craft keeps writing its existing `members:internal_media:*` keys. The two run
  in parallel — different namespaces, no collision — so the API can ship
  incrementally without touching the live site.
- The members web area keeps reading Craft's keys until cutover. **Cutover is a
  key-prefix flip in 4 front-end reader files** (no type changes — the JSON
  shape is identical), mirroring the 7-file prefix flip the public migration
  did. It stays deferred until the API output is verified in dev.
- **Audio is shared via the volume, not the prefix.** Both writers store audio
  into the same `files-above-webroot-volume` at `internal-audio/{slug}/{filename}`,
  and the web audio route resolves files from that volume regardless of which key
  namespace powers the front-end. So audio is not part of the prefix switch — it
  just needs the files present in the volume (see Workstream 2).

## Current state snapshot

What already exists:

- **Web (done):** `web/app/members/` — login (`PostLoginForm.ts`, config-based
  `MEMBER_EMAIL_ADDRESS`/`MEMBER_PASSWORD` → `member` HMAC cookie), the
  internal-media pages (`GetPageData.ts` readers), and the gated audio route
  `web/app/members/internal-media/audio/[slug]/[filename]/route.ts` (validates
  `member` cookie, streams from `/app/filesAboveWebroot/internal-audio/{slug}/{filename}`
  with HTTP range support).
- **Craft (to be retired):** the `internalMessages` channel + `internalMessageSeries`
  category group, the audio asset field storing into `filesAboveWebroot/internal-audio/{slug}/`,
  and `GenerateInternalMediaPagesForRedis.php`.
- **API public messages slice** (`api/src/Messages/`) — the template this work
  copies: model, repository, admin CRUD, `Generate/` Redis writers, `Transfer/`
  import commands.
- **Profiles already imported** into the API `profiles` table (shared speaker
  pool — no new profile work needed).

What does **not** exist yet: any internal-messages code in the API.

## Design decisions (locked in)

- **Permissions:** reuse the existing `EDIT_MESSAGES` role /
  `RequireEditMessagesRoleMiddleware`. No new role in `UserRole` and no
  auth-service change. Whoever can edit public messages can edit internal ones.
- **Search:** **no Elasticsearch, anywhere.** The members area only browses Redis
  pages with speaker/series filters (no search box), matching what Craft produces
  today. The **admin** list gets **pagination but no search** (the public
  messages admin has both; internal omits the search half — see Workstream 3),
  since there's no Elasticsearch index on the member side to back it.
- **Series pool:** **separate** from public messages. New `internal_series`
  table; do not reuse the public `series` table.
- **Speakers:** **shared** with public messages — `speaker_id` FK → existing
  `profiles` table.
- **Redis keys:** the API writes a **new `api-members:internal_media:` namespace**
  (Craft's `members:internal_media:` with the same `api-` prefix the public
  migration used: `messages:` → `api-messages:`). The API and Craft write
  disjoint namespaces in parallel; the web flips prefixes at cutover. *(Confirm
  the exact prefix string on review — `api-members:internal_media:` is the
  consistent choice.)* The JSON shape inside each key is identical to Craft's so
  the front-end needs no type changes, only a prefix swap.
- **Member auth:** unchanged — the existing config-based member login + `member`
  cookie stays as-is. Out of scope.
- **Data import:** yes — a one-time, idempotent HTTP-endpoint-based import from
  Craft, following the existing `api/src/Transfer/` pattern.

### The audio storage difference (the one genuinely novel piece)

Public audio is flat at `/uploads/audio/{slug}.mp3`, served statically with no
auth. Internal audio is different and must stay compatible with the already-built
web route:

- **Path:** `filesAboveWebroot/internal-audio/{slug}/{filename}` — a subfolder
  per entry slug, original filename preserved (e.g.
  `internal-audio/2026-05-17-i-belong-to-god-week-48/SS20260517_IBelongToGod_wk48.mp3`).
- **DB columns:** store `audio_file_name` (the filename only) and
  `audio_file_size` (bytes). *Not* the public-messages `audio_path = {slug}.mp3`
  convention.
- **Docker change required:** the `api`, `api-queue-consumer`, and
  `api-schedule-runner` services currently mount only `uploads-volume`. They must
  **also mount `files-above-webroot-volume`** (e.g. at `/var/www/filesAboveWebroot`)
  in both `docker/docker-compose.dev.yml` and `docker/docker-compose.prod.yml`,
  so the API can write internal audio into the same volume the web reads. This is
  the key infra edit and a prerequisite for the audio-storage and import work.

## Workstreams

### 1. Data model + migrations

Locality: `api/src/InternalMessages/` (new feature slice, structured like
`api/src/Messages/`).

- **Migration — `internal_messages` table:** `id` (UUID PK), `enabled` (bool),
  `date` (datetime), `title`, `slug`, `speaker_id` (UUID → `profiles`),
  `passage`, `series_id` (UUID → `internal_series`), `description`,
  `audio_file_name`, `audio_file_size` (int unsigned null). Index the same
  columns the messages table indexes.
- **Migration — `internal_series` table:** `id` (UUID PK), `title`, `slug`.
- Entity `InternalMessage`, `NewInternalMessage`, typed collection
  `InternalMessages`, `InternalMessageValidation` (title required, audio
  required), `InternalMessagesRepository`, `InternalSeries` / `InternalSeriesRepository`,
  and a `Persistence/Transformer` — copied/adapted from the Messages slice.
  Reuse the US/Central timezone handling from `Messages/Persistence/Transformer.php`.

### 2. Audio storage

Locality: `api/src/InternalMessages/Persistence/`.

- `InternalMessageAudioFileStorage` — mirrors
  `Messages/Persistence/Persist/MessageAudioFileStorage.php` (base64 decode + MP3
  validation), but writes to `{filesAboveWebrootRoot}/internal-audio/{slug}/{filename}`
  (subfolder-per-slug, preserved/derived filename) and deletes the slug subfolder
  on entry delete.
- Capture `audio_file_name` + `audio_file_size` and persist them on the row
  (same "metadata captured before the row write" approach the messages slice
  settled on).
- **New uploads:** generate `{slug}.mp3` as the filename (clean, collision-free).
  The import preserves Craft's original filenames. Either is fine as long as
  `audioFileName` in Redis matches the stored file. *(Confirm preference on
  review.)*
- Requires the `files-above-webroot-volume` mount from the Design Decisions
  section.

### 3. Admin CRUD

Locality: `api/src/InternalMessages/Admin/`.

Mirror `api/src/Messages/Admin/`, all gated by the existing
`RequireEditMessagesRoleMiddleware`:

- `GET  /admin/internal-messages` — **paginated** list (see below)
- `GET  /admin/internal-messages/{id}` — edit form
- `POST /admin/internal-messages/new`
- `POST /admin/internal-messages/{id}` — update
- `POST /admin/internal-messages/delete`
- `GET/POST /admin/internal-series` CRUD — mirrors `/admin/series`.

Register all in `api/config/Events/ApplyRoutes.php`.

**Admin list pagination (mirror public messages, omit search).** The public
messages admin was recently given paginated + searchable list. Internal messages
should match the **pagination** but **leave search off** (no Elasticsearch on the
member side). The two are cleanly separable in the existing code:

- `GetInternalMessagesListAction` (mirrors `GetMessagesListAction`): read `?page=N`
  from the query, **always** call `repository->findAll()` (skip the
  `keyword`/search branch entirely), build a `Pagination` value object
  (`api/src/Pagination/Pagination.php`) with per-page = **100** (match the public
  admin's `PER_PAGE`), and `sliceToPage()` the collection.
- Response shape mirrors `PaginatedMessages` — a `PaginatedInternalMessages`
  serializing `{ currentPage, totalPages, totalResults, entries }`.
- The `InternalMessages` collection needs `sliceToPage(page, perPage)` and
  `count()` (copied from `Messages`).

**Web admin UI:** add a `web/app/admin/internal-messages/` area mirroring
`web/app/admin/messages/`, including the pagination but **not** the search form:

- Routes `/admin/internal-messages` (page 1) and
  `/admin/internal-messages/page/[pageNum]`, matching the messages pattern.
- `GetInternalMessages.ts` sends only `?page=N` (no `keyword`); response type
  `{ currentPage, totalPages, totalResults, entries }`.
- Reuse the shared `web/app/Pagination/Pagination.tsx` component with
  `baseUrl="/admin/internal-messages"` and an empty `queryString` (no keyword to
  preserve).
- **Do not** copy `MessagesSearchForm.tsx` or wire a `keyword` param.
- Add a sidebar link in `web/app/admin/Layout/Sidebar.tsx` under the same
  `EDIT_MESSAGES` role gate.

(Front-end admin only — the member-facing pages are untouched.)

### 4. Redis generation (the writer the web already expects)

Locality: `api/src/InternalMessages/Generate/`.

Build small role-based collaborators (per `AGENTS.md`) mirroring
`api/src/Messages/Generate/`, writing to the **new `api-members:internal_media:`
namespace** with the **exact same JSON shape** the web reader expects. Centralize
the key strings in an `InternalMediaRedisKey` object (mirroring `MessagesRedisKey`).
Verified byte-for-byte against Craft's `GenerateInternalMediaPagesForRedis.php`
and the web's TypeScript types (`web/app/audio/MessagesPageData.ts`,
`web/app/audio/Entry.ts`):

| Key (new API namespace) | Craft equivalent | Payload |
|-----|-----|-----|
| `api-members:internal_media:page:{n}` | `members:internal_media:page:{n}` | envelope + `entries[]` |
| `api-members:internal_media:slug:{slug}` | `members:internal_media:slug:{slug}` | `{ "entry": { … } }` |
| `api-members:internal_media:by:{speakerSlug}:{n}` | `members:internal_media:by:{speakerSlug}:{n}` | envelope + `entries[]` + `byName` + `bySlug` |
| `api-members:internal_media:series:{seriesSlug}:{n}` | `members:internal_media:series:{seriesSlug}:{n}` | envelope + `entries[]` + `seriesName` + `seriesSlug` |

**Envelope:** `currentPage, perPage, totalResults, totalPages, pagesArray,
prevPageLink, nextPageLink, firstPageLink, lastPageLink, entries`.

**Entry:** `uid, title, slug, postDate, postDateDisplay, by{title,slug}|null,
text, series{title,slug}|null, audioFileName, audioFileSize`.

Locked specifics:

- Per-page = **25**.
- `postDate` format `Y-m-d H:i:s`; `postDateDisplay` format `F j, Y` (US/Central).
- `pagesArray` elements are `{ label, target, isActive }` (`label` numeric).
- `by` / `series` are `null` when absent.
- `audioFileName` = filename only (no path prefix); `audioFileSize` = bytes (or null).
- **Orphan cleanup:** delete stale `api-members:internal_media:*` keys not in
  the current generation (scoped to the API's own prefix — never touch Craft's
  `members:internal_media:*` keys while running in parallel).
- An `InternalMediaEntryJsonFactory` produces the shared entry shape; the page
  writers depend on it.

Wire into the runtime like the messages generator:

- `EnqueueGenerateInternalMediaPagesForRedis` + a queue job.
- A `ScheduleItem` (match the messages cadence, ~5 min) in `ScheduleFactory`.
- Per-event regeneration in internal-message create/update/delete (and
  internal-series mutations, and profile renames — a renamed speaker invalidates
  keys).
- A manual `internal-messages:generate-redis-pages` CLI command for triggering.

### 5. One-time import from Craft

Locality: `craft-cms/src/Transfer/` + `api/src/Transfer/InternalMessages/`,
`api/src/Transfer/InternalSeries/`.

Follow the existing transfer pattern (HTTP-endpoint based, idempotent by UUID —
no direct DB access). Profiles are already imported, so speakers resolve by
existing UUID; **no profile import needed.**

- **Craft side:** `GetTransferInternalSeries` and `GetTransferInternalMessages`
  (mirror `GetTransferSeries` / `GetTransferMessages`), registered in
  `craft-cms/config/slim/routes.php`. Map the `internalMessages` entries
  (title, postDate→US/Central, slug, `profile[0].uid`→speakerId,
  `internalMessageSeries[0].uid`→seriesId, messageText, shortDescription,
  `internalAudio[0].filename` + `.size`) and the `internalMessageSeries` category
  group (uid, title, slug) to JSON.
- **API side:** `transfer:import:internal-series` then
  `transfer:import:internal-messages` commands, registered in
  `api/config/Events/ApplyCommands.php`, run via `dev api "…"`.
- **Audio files:** already present in the shared `files-above-webroot-volume`
  (once the API mounts it), so **no copying** — the import records
  `audio_file_name` and `audio_file_size` (from the Craft endpoint, or stat the
  file at `{filesAboveWebrootRoot}/internal-audio/{slug}/{filename}`).

## Verification

Because the API writes its own namespace, all of this happens with Craft still
live and untouched:

- Run `internal-messages:generate-redis-pages`; inspect with
  `redis-cli KEYS 'api-members:internal_media:*'`. Cross-check the JSON in
  `api-members:internal_media:page:1` against Craft's `members:internal_media:page:1`
  — confirm byte-for-byte shape parity (only the key prefix should differ).
- Run the import commands against the Craft transfer endpoints; confirm the
  internal_messages / internal_series rows hydrate and audio sizes populate.
- Create / edit / delete an internal message in the admin UI; confirm the
  `api-members:internal_media:*` keys regenerate and orphans are cleaned (and
  Craft's `members:internal_media:*` keys are left alone).
- **Cutover smoke test (in a throwaway/dev flip):** temporarily point the 4 web
  readers at the `api-` prefix, load `/members/internal-media` (plus a detail, a
  by-speaker, and a series page) logged in as a member, and confirm pages render
  and audio plays. Compare against the current Craft-served pages — they should
  be identical since both read the same envelope shape.

## Front-end cutover — the prefix flip (deferred until verified in dev)

Keep both writers running in parallel the whole time so output can be compared.
The cutover itself is small and reversible: flip the Redis key prefix in the **4
reader files** under `web/app/members/internal-media/`. No type changes — the
JSON envelope/entry shape is identical (`web/app/audio/MessagesPageData.ts`,
`web/app/audio/Entry.ts` are unchanged).

| File | Craft key | API key |
|------|-----------|---------|
| `GetPageData.ts:11` | `members:internal_media:page:{n}` | `api-members:internal_media:page:{n}` |
| `[slug]/GetPageData.ts:13` | `members:internal_media:slug:{slug}` | `api-members:internal_media:slug:{slug}` |
| `by/[slug]/GetPageData.ts:17` | `members:internal_media:by:{slug}:{n}` | `api-members:internal_media:by:{slug}:{n}` |
| `series/[slug]/GetPageData.ts:17` | `members:internal_media:series:{slug}:{n}` | `api-members:internal_media:series:{slug}:{n}` |

There are no options/most-recent keys to flip (the internal-media area has no
filter-options dropdown, unlike public messages). There is no search endpoint to
flip (no Elasticsearch). **Audio is untouched** — it resolves from the shared
volume regardless of prefix (see "How this mirrors…"). After flipping, exercise
the listing/detail/by/series pages logged in as a member and `tsc` + `eslint`
clean.

## Craft teardown & prod (deferred — requires explicit authorization)

Only after the front-end is verified on the API prefix in dev:

1. **Stop Craft's internal-media schedule + entry-event subscribers** so Craft
   stops writing the `members:internal_media:*` keys and stops being the audio
   writer. (Keeping both running in parallel until this point is what lets us
   compare.)
2. Retire the Craft `internalMessages` section, `internalMessageSeries` group,
   the internal-audio asset field, `GenerateInternalMediaPagesForRedis.php`, and
   the Craft transfer endpoints once nothing else uses them. Drop the dead Craft
   `members:internal_media:*` keys.
3. Prod env / prod volume + swarm changes (the `files-above-webroot-volume`
   mount on the API services in `docker-compose.prod.yml`).

## Execution order

Each step is independently shippable:

1. **Workstream 1** — model + migrations.
2. **Docker** — mount `files-above-webroot-volume` into the API services
   (prerequisite for audio + import).
3. **Workstream 2** — audio storage.
4. **Workstream 3** — admin CRUD (API + web admin UI).
5. **Workstream 4** — Redis generation, then wire into schedule + CUD events.
6. **Workstream 5** — Craft transfer endpoints + API import commands; run the
   import.

Then the front-end prefix flip (4 files), and only after that is verified in dev,
the Craft teardown. Both are deferred — every step above ships while Craft stays
live on its own keys.
