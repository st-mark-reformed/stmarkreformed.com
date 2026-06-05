# Resources — Craft → New API Migration

## Context

The **Resources** section is a **public, paginated** channel still owned by the old Craft
CMS (`craft-cms`). It is already a strangler-fig'd page: Craft writes the data into Redis
(`resources:page:{n}` and `resources:slug:{slug}`) and the **Next.js `web` app already
renders `/resources`** by reading those keys (`web/app/resources/`). So this is a
**News-style cutover** — the front-end templates do not change; we only move ownership of
the data to the new `api` app and switch the read prefix.

The one wrinkle beyond News: each Resource carries a **collection of file downloads**
(Craft's `resourceDownloads` Matrix → `resourceDownload` blocks → a `file` asset each).
These files are **public**, live in the shared uploads volume at
`public/uploads/general/resources/{slug}/{filename}`, and are served by the web app
statically at `/uploads/general/resources/{slug}/{filename}` (see
`web/app/resources/ResourceListing.tsx:37`). Keeping that exact path preserves all ~155
existing entries' files with **no binary copy** and **no front-end change**.

This plan brings Resources fully onto the new app: a typed data model + table, a Craft
transfer endpoint, an import command, Redis generation under a new **`api-resources:`**
prefix (so Craft and the new API run side-by-side), a front-end read cutover, and a full
web admin UI with file uploads — gated by a new `EDIT_RESOURCES` role.

### Decisions (confirmed)
- **Scope:** Full parity — migration + read cutover + admin UI + role.
- **SEO fields skipped.** Craft's `seoTitle` / `seoDescription` / `customShareImage` /
  `searchEngineIndexing` are **not** read by the current front-end; we do not migrate them
  or add admin inputs (matches the News migration deferring OpenGraph).
- **Downloads storage:** A single `downloads` JSON column decoded into a typed
  `ResourceDownloads` collection of `ResourceDownload` value objects (downloads are wholly
  owned by the resource; co-location over normalization). Each carries the original
  `filename` (front-end displays it and builds the URL from `slug` + `filename`).
- **File path preserved:** New uploads write to `public/uploads/general/resources/{slug}/`,
  the same path Craft used, so existing files stay valid and the front-end URL is unchanged.
- **Redis keys:** New `api-resources:page:{n}` and `api-resources:slug:{slug}` (mirrors the
  `api-news:` convention) so the new API and Craft coexist during cutover.

### Reference implementations to mirror
- **News** (`api/src/News/`, `web/app/admin/news/`) — the closest analog: public + paginated,
  `page:{n}` + `slug:{slug}` Redis keys, admin CRUD, transfer endpoint, import command,
  role/middleware, schedule registration, slug derived from title. **Copy the News slice and
  add a downloads collection.**
- **Hymns of the Month** (`api/src/HymnsOfTheMonth/`) — the model for the **repeatable
  file-upload rows** in the admin UI (practice tracks) and the file-storage helper shape.
- **Messages public file storage** (`api/src/Messages/Persistence/Persist/PersistMessageAudioFile.php`
  + `MessageAudioFileStorage`) — the model for writing uploads into the **public**
  `/var/www/public/uploads/...` volume (vs. Hymns' above-webroot files).

---

## Step 1 — Database migration
New Phinx migration `api/config/Data/Migrations/<ts>_create_resources_table.php` (mirror
`<ts>_create_news_table.php`). Table `resources`:

| column | type | notes |
|---|---|---|
| `id` | UUID PK | preserve Craft uid on import |
| `enabled` | bool, default true, indexed | from Craft status |
| `date` | datetime, indexed | post date; drives newest-first ordering |
| `title` | string, indexed | |
| `slug` | string, indexed | preserved from Craft / derived from title for new |
| `body` | text | rich-text HTML, verbatim |
| `downloads` | text/json | JSON array of `{filename}` |

(Written first so it can be committed, deployed, and run standalone ahead of the rest.)

## Step 2 — New role (both enums)
- `api/src/Auth/UserRole.php` — add `case EDIT_RESOURCES;`
- `auth/src/User/UserRole.php` — add the same case (so it appears in user management).
- New `api/src/Resources/Admin/RequireEditResourcesRoleMiddleware.php` (mirror
  `RequireEditNewsRoleMiddleware`).

## Step 3 — API feature slice (`api/src/Resources/`)
Mirror the News slice. Core domain:
- `ResourceItem.php` — readonly immutable entity (id, isEnabled, date US/Central, title,
  slug, body, downloads), `with*()` builders, validation in constructor.
- `NewResourceItem.php` — creation DTO (nullable slug + optional trailing UUID for import).
- `ResourceItems.php` — typed collection (newest-first sort, `sliceToPage`, `findById`,
  keyword filter).
- `ResourceDownload.php` + `ResourceDownloads.php` — typed value object + collection
  (validates item types in constructor; **no raw arrays across boundaries**).
- `ResourceItemValidation.php` (`title` required; `body` optional — Resources can be a
  download-only entry), `ResourceItemResult.php`.
- `CreateResourceSlug.php` — slugify **title only** via `cocur/slugify` (mirror
  `CreateNewsSlug`); used only when slug is null/unset.

Persistence (`api/src/Resources/Persistence/`, mirror `News/Persistence/`):
- `ResourceItemRecord.php` (+ `...Records.php`) with `TABLE_NAME = 'resources'`.
- `Transformer.php` — record ↔ entity, JSON-encode/decode `downloads` into
  `ResourceDownloads`.
- `FindAll.php` (ORDER BY date DESC), `FindById.php`, plus `Create/`, `Persist/`, `Delete/`
  transaction + PDO wrappers.
- `ResourceFileStorage.php` (under `Persistence/Persist/`) — base64 data URI → decode →
  write to `/var/www/public/uploads/general/resources/{slug}/{filename}`, preserving the
  **original (sanitized) filename**; handle per-file delete and empty-`{slug}`-dir cleanup.
  Modeled on `MessageAudioFileStorage` (public uploads) but keyed by `{slug}` like
  `HymnFileStorage`. All file types allowed (Craft's `file` field allowed all kinds).
- `ResourcesRepository.php` — facade (`create`, `persist`, `delete`, `findAll`, `findById`).

## Step 4 — Redis generation (`api/src/Resources/Generate/`)
Mirror `News/Generate/`:
- `ResourcesRedisKey.php` — namespace `api-resources:`, keys `page:{n}` and `slug:{slug}`.
- `GenerateResourcesPagesForRedis.php` — write per-slug keys for **all enabled** items
  (permalink always live), and `page:{n}` keys for items with `date <= now`, newest-first,
  **12/page** (matches Craft + `FindResourceItemsByPage`). Pipeline writes + orphan cleanup
  (`KEYS api-resources:*`, delete keys not re-written), exactly like News.
- `ResourceEntryJsonFactory.php` — emit the **exact payload shape the front-end already
  consumes**: per-entry `{ title, slug, body, resourceDownloads: [{filename}] }`; page
  payload `{ currentPage, perPage, totalResults, totalPages, pagesArray, prev/next/first/last
  PageLink, entries:[...] }`; slug payload `{ entry: {...} }`. (Confirm exact shape against
  `web/app/resources/repository/ResourceItem.ts` + `ResourcesItemsReturn.ts`.)
- `GenerateResourcesPagesForRedisCommand.php`, `EnqueueGenerateResourcesPagesForRedis.php`.
- Register command in `api/config/Events/ApplyCommands.php`; add enqueue to
  `api/config/ScheduleFactory.php` (5-min cadence, matching siblings) so future-dated
  resources roll into listings automatically.

## Step 5 — Admin API actions (`api/src/Resources/Admin/`)
Mirror News admin, all gated by `RequireEditResourcesRoleMiddleware`, routes in
`api/config/Events/ApplyRoutes.php`:
- `GET /admin/resources/has-edit-role`
- `GET /admin/resources` (paginated list; keyword search on title)
- `POST /admin/resources/new`
- `GET /admin/resources/edit/{id}`
- `PATCH /admin/resources/edit/{id}`
- `DELETE /admin/resources`

Factories parse the payload: `isEnabled`, `date`, `title`, `slug`, `body`, and a
`downloads` array of `{filename, base64?|existingFilename}`. On create/edit, run new files
through `ResourceFileStorage`, drop removed files, then persist + enqueue Redis generation.

## Step 6 — Web admin UI (`web/app/admin/resources/`)
Mirror `web/app/admin/news/` (list, paginated route, new, edit, delete, search, role guard,
`GetResources.ts`, types, submit actions). Fields: **Title**, **Slug** (auto-fill from title
while untouched, new posts only), **Date**, **Enabled** toggle, **Body** via the existing
`RichText` component (`web/app/admin/Forms/RichText.tsx`).
- **Downloads:** repeatable rows (mirror Hymns' practice-tracks UI in
  `web/app/admin/hymns-of-the-month/`) — each row is a file input; add/remove rows;
  co-located handlers in the component. Existing downloads show their current filename; only
  newly chosen files send base64 (via existing `FileToBase64.ts`). The original filename is
  sent alongside the base64 so the stored file and the displayed/download name match.
- Nav: add a Resources item to `web/app/admin/Layout/Sidebar.tsx` gated on `EDIT_RESOURCES`,
  and extend the `activeNav` union in `web/app/admin/Layout/AdminLayout.tsx` with
  `'resources'`.

## Step 7 — Craft transfer endpoint
- `craft-cms/src/Transfer/GetTransferResources.php` (mirror `GetTransferNews.php`),
  registered in `craft-cms/config/slim/routes.php` → `GET /transfer/resources`.
- Reuse the existing `RetrieveResources` extraction
  (`craft-cms/src/Http/Response/Media/Resources/RetrieveResources.php`): per entry emit
  `id` (entry uid), `date` (`Y-m-d H:i:s`, US/Central), `enabled` (status), `title`, `slug`,
  `body` (HTML verbatim), and `resourceDownloads` (`[{filename}]` pulled from each
  `resourceDownload` block's `file` asset). Preserve the Craft `slug` (existing file folders
  and permalinks embed it).

## Step 8 — API import command
- `api/src/Transfer/Resources/ImportResourcesFromCraftCommand.php` (mirror
  `ImportNewsFromCraftCommand`), registered in `ApplyCommands.php` as
  `transfer:import:resources`.
- GET Craft `/transfer/resources` (base URL from `RuntimeConfigOptions::APP_API_URL`).
  Upsert by `uid`: preserve Craft `slug` (critical — existing permalinks and file folders
  must not change), store `downloads` as `[{filename}]`. **No binary copy** — files already
  live in the shared uploads volume at `general/resources/{slug}/{filename}`. Create-if-
  missing / sync-if-changed like the News import. Enqueue Redis generation after the run.

## Step 9 — Front-end read cutover (do last, after verifying generation)
Switch the public read prefix from Craft's `resources:` to `api-resources:`:
- `web/app/resources/repository/FindResourceItemsByPage.ts:10` → `api-resources:page:${pageNum}`
- `web/app/resources/repository/FindResourceItemBySlug.ts:10` → `api-resources:slug:${slug}`

The `ResourceItem` / `ResourcesItemsReturn` interfaces and all templates stay unchanged
because the new payload matches the existing shape. This is the single switch that moves the
live page onto the new system. (Craft as a whole is powered down later, once all sections
are migrated — not per section.)

---

## Docker volumes (verified — no changes needed)
The `uploads-volume` is already shared between the API and web in both environments, so files
the API writes are immediately served by web:
- **Dev** (`docker/docker-compose.dev.yml`): web mounts
  `../craft-cms/public/uploads:/app/public/uploads` (line 56); api + queue-consumer +
  schedule-runner mount the same host dir at `/var/www/public/uploads` (lines 79, 114, 134).
- **Prod** (`docker/docker-compose.prod.yml`): `uploads-volume` mounts at `/app/public/uploads`
  on web (line 92) and `/var/www/public/uploads` on every api container (lines 132, 158, 179,
  248, 273, 298); volume declared at line 379.

API writes to `/var/www/public/uploads/general/resources/{slug}/{filename}`; web serves it at
`/uploads/general/resources/{slug}/{filename}`. **No docker edits required** — confirm the
directory is writable by the api container's user during verification.

---

## Verification
No automated test suite in this project — verify via static analysis + manual run
(phpcs, phpstan, eslint, tsc must all pass with zero warnings).

1. **Static:** run phpcs/phpstan (api + auth) and eslint/tsc (web); zero warnings.
2. **Migration:** `docker exec stmark-api php cli migrate:up`; confirm `resources` table shape.
3. **Import:** `docker exec stmark-api php cli transfer:import:resources` against local Craft;
   expect ~155 rows; spot-check that slugs exactly match Craft and that `downloads` JSON
   round-trips into `ResourceDownloads`.
4. **Redis:** run the generate command; inspect `api-resources:page:1` and a
   `api-resources:slug:{slug}` key (`docker exec stmark-redis redis-cli KEYS 'api-resources:*'`)
   — payload shape matches `ResourceItem.ts` / `ResourcesItemsReturn.ts`.
5. **Front-end (public):** temporarily point one repository at `api-resources:`, load
   `/resources`, a paginated page, and a single resource; confirm listing, body, and **file
   downloads resolve** at `/uploads/general/resources/{slug}/{filename}`. Use the
   `docker exec stmark-web touch` trick if the bind-mount misses an edit.
6. **Admin:** as a user with `EDIT_RESOURCES`, create a resource with a title, body, and 2
   download files; confirm files land at `public/uploads/general/resources/{slug}/`, Redis
   regenerates, and the public page shows them. Edit (add/remove a download) and delete;
   confirm file cleanup and Redis pruning.
7. Confirm the role appears in the user-management UI and gates both the API routes and the
   admin sidebar item.
8. **Cutover:** flip both repository prefixes to `api-resources:`; smoke-test `/resources`,
   a deep page, and a permalink with downloads.

## Notes / risks
- **Slug exactness is load-bearing.** Existing file folders are keyed by Craft's slug
  (`general/resources/{slug}/...`); the import must preserve Craft slugs verbatim (Step 8,
  checked in Verification step 3) or download URLs break.
- **Editing a slug** on a resource that has files: the storage layer writes under the
  *current* slug, so a slug change would orphan files at the old path and 404 the downloads.
  Mitigation: have `ResourceFileStorage` relocate the `{slug}` folder when the slug changes,
  or treat slug as effectively stable in the admin UI. Decide during implementation; flag the
  edge in the form.
- **Filename collisions / sanitization:** original filenames are user-visible and used in the
  URL. Sanitize (strip path separators, normalize) but keep them human-readable; de-dupe
  within a single resource's folder if two uploads share a name.
- **`body` optional:** unlike News, a Resource may be a pure download (no body). Validation
  requires `title` only; the listing card already handles the download-only case
  (`ResourceListing.tsx:31`).
