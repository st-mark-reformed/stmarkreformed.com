# Hymns of the Month — Craft → New API Migration

## Context

"Hymns of the Month" is a member-protected section currently owned by the old Craft CMS
(`craft-cms`). Craft writes the data into Redis (`members:hymns_of_the_month:index` and
`members:hymns_of_the_month:slug:{slug}`) and the Next.js `web` app reads those keys. The
uploaded files (one sheet-music file + N practice tracks) live in the shared
`filesAboveWebroot` volume under `{slug}/music/...` and `{slug}/audio/...`, served by the
member-token-gated download route at `web/app/members/hymns-of-the-month/download/[...filepath]`.

This migrates the section onto the new app, exactly as was just done for the Pastors Page. The
plan brings the section fully onto the new `api` app: a typed data model + table, a Craft
transfer endpoint, an import command, Redis generation (under a new `api-members:` prefix),
a front-end read cutover, and a full web admin UI with file uploads — gated by a new
`EDIT_HYMNS_OF_THE_MONTH` role.

Two structural simplifications requested for the new model:

1. **Date is a month, not a day.** Craft only had a day-of-month field, worked around by
   always setting the 1st. The new model stores a first-of-month `DATETIME` and is edited
   with a month picker.
2. **No separate title or slug fields.** Both are *derived from the date* and persisted:
   - `title` = `date->format('F, Y')` → e.g. `January, 2024`
   - `slug`  = `Slugify()->slugify(title)` → e.g. `january-2024`

   This reproduces Craft's existing slugs exactly (Craft's title format was `{date|date('F, Y')}`
   and its slug was the slugified title), so **permalinks and existing file paths
   (`{slug}/music/...`, `{slug}/audio/...`) stay valid and no binary files need to move.**

### Decisions (confirmed with TJ)
- **Scope:** Full parity — migration + read cutover + admin UI + role.
- **Practice tracks storage:** A single `practice_tracks` JSON column, decoded into a typed
  `HymnPracticeTracks` collection of `HymnPracticeTrack` value objects (tracks are wholly
  owned by the hymn; co-location over normalization).
- **Redis keys:** New `api-members:hymns_of_the_month:...` keys (mirrors the `api-pastorsPage`
  convention) so the new API and Craft can run side-by-side during cutover.

### Reference implementations to mirror
- **Pastors Page** (`api/src/PastorsPage/`) — overall feature-slice shape, admin actions,
  transfer endpoint, import command, role/middleware, Redis generation, web admin UI.
- **InternalMessages** (`api/src/InternalMessages/`) — the closest analog for
  **member-protected, above-webroot file handling**: base64 upload → validate → write to
  `filesAboveWebroot`, member-token download route, `api-members:` Redis namespace, and the
  "metadata-only" import (files already exist in the shared volume).

---

## Step 1 — New role (both enums)
- `api/src/Auth/UserRole.php` — add `case EDIT_HYMNS_OF_THE_MONTH;`
- `auth/src/User/UserRole.php` — add the same case (so it appears in user management).
- New `api/src/HymnsOfTheMonth/Admin/RequireEditHymnsOfTheMonthRoleMiddleware.php`
  (mirror `RequireEditPastorsPageRoleMiddleware`).

## Step 2 — Database migration
New Phinx migration `api/config/Data/Migrations/<ts>_create_hymns_of_the_month_table.php`
(mirror `20260603000002_create_pastors_page_table.php`). Table `hymns_of_the_month`:

| column | type | notes |
|---|---|---|
| `id` | UUID PK | preserve Craft uid on import |
| `enabled` | bool, default true, indexed | |
| `date` | datetime, indexed | first-of-month |
| `slug` | string, indexed | derived `january-2024` |
| `hymn_psalm_name` | text | from Craft `hymnPsalmName` |
| `music_sheet_path` | string, nullable | relative path within `filesAboveWebroot`, e.g. `january-2024/music/sheet.pdf` |
| `practice_tracks` | text/json | JSON array of `{title, path}` |

(`title` is NOT stored — derived from `date` in code, single source of truth.)

## Step 3 — API feature slice (`api/src/HymnsOfTheMonth/`)
Mirror the Pastors Page slice. Core domain:
- `HymnOfTheMonthItem.php` — immutable entity (id, isEnabled, date, hymnPsalmName,
  musicSheetPath, practiceTracks). `title()` / `slug()` are **derived** from `date` via a
  small `HymnMonthTitle`/`CreateHymnSlug` helper (uses `cocur/slugify`, as
  `CreatePastorsPageSlug` does).
- `NewHymnOfTheMonthItem.php` — creation DTO (nullable slug + optional trailing UUID for import).
- `HymnOfTheMonthItems.php` — typed collection (`filter`, `sliceToPage`, `map`, `findById`).
- `HymnPracticeTrack.php` + `HymnPracticeTracks.php` — typed value object + collection
  (validates item types in constructor; **no raw arrays across boundaries**).
- `HymnOfTheMonthItemValidation.php`, `HymnOfTheMonthItemResult.php`.

Persistence (`HymnsOfTheMonth/Persistence/`, mirror Pastors Page):
- `HymnOfTheMonthItemRecord.php` (+ `...Records.php`) with `TABLE_NAME = 'hymns_of_the_month'`.
- `Transformer.php` — record ↔ entity, JSON-encode/decode `practice_tracks` into
  `HymnPracticeTracks`.
- `FindAll.php` (ORDER BY date DESC), `FindById.php`, plus `FindBySlug.php` if needed.
- `Create/`, `Persist/`, `Delete/` wrappers + PDO classes.
- `HymnFileStorage.php` (under `Persistence/Persist/`) — base64 → validate → write to
  `filesAboveWebroot/{slug}/music/{filename}` and `{slug}/audio/{filename}`; handle delete +
  empty-dir cleanup. Modeled on `InternalMessageAudioFileStorage` but **keeps the existing
  `{slug}/music` & `{slug}/audio` layout** (not `internal-audio/{slug}`) so migrated files
  resolve unchanged. Sheet music validated as PDF; practice tracks validated as MP3/audio.
- `HymnsOfTheMonthRepository.php` — facade (`create`, `persist`, `delete`, `findAll`,
  `findById`).

## Step 4 — Redis generation (`api/src/HymnsOfTheMonth/Generate/`)
Mirror `InternalMessages/Generate` (member namespace) + Pastors Page generation:
- `HymnsOfTheMonthRedisKey.php` — namespace `api-members:hymns_of_the_month:`, keys
  `:index` and `:slug:{slug}`.
- `GenerateHymnsOfTheMonthForRedis.php` — write index + per-slug keys; prune orphaned slug
  keys (same logic as the current Craft generator).
- `HymnEntryJsonFactory.php` — emit the **same payload shape the front-end already consumes**
  (`title, slug, hymnPsalmName, content, musicSheetFilePath, musicSheetFileName?,
  practiceTracks:[{title,path}]}`), where `content` is the derived
  `"Resources and tools for learning the hymn of the month: " . hymnPsalmName`.
- `GenerateHymnsOfTheMonthForRedisCommand.php`, `EnqueueGenerateHymnsOfTheMonthForRedis.php`.
- Register command in `api/config/Events/ApplyCommands.php`; add enqueue to
  `api/config/ScheduleFactory.php` (5-min cadence, matching siblings).

## Step 5 — Craft transfer endpoint
- `craft-cms/src/Transfer/GetTransferHymnsOfTheMonth.php` (mirror `GetTransferPastorsPage`),
  registered in `craft-cms/config/slim/routes.php` → `GET /transfer/hymns-of-the-month`.
- Reuse the existing `RetrieveHymns` extraction (`craft-cms/src/Http/Response/Members/HymnsOfTheMonth/RetrieveHymns.php`):
  per entry emit `id` (entry uid), `date` (Y-m-d H:i:s, US/Central), `enabled`,
  `hymnPsalmName`, `musicSheetFilePath` (`$musicSheet?->path`), and `practiceTracks`
  (`[{title, path}]`). **slug/title intentionally omitted** — derived API-side from `date`.

## Step 6 — API import command
- `api/src/Transfer/HymnsOfTheMonth/ImportHymnsOfTheMonthFromCraftCommand.php`
  (mirror `ImportPastorsPageFromCraftCommand`), registered in `ApplyCommands.php` as
  `transfer:import:hymns-of-the-month`.
- GET `/transfer/hymns-of-the-month`, preserve Craft uid, parse date → first-of-month
  `DateTimeImmutable` (US/Central), derive slug, store `musicSheetPath` + `practiceTracks`
  as-is. **No binary copy** — files already live in the shared `filesAboveWebroot` volume at
  the same `{slug}/...` paths. Create-if-missing / sync-if-changed like the Pastors Page import.

## Step 7 — Admin API actions (`api/src/HymnsOfTheMonth/Admin/`)
Mirror Pastors Page admin, all gated by `RequireEditHymnsOfTheMonthRoleMiddleware`, routes in
`api/config/Events/ApplyRoutes.php`:
- `GET /admin/hymns-of-the-month/has-edit-role`
- `GET /admin/hymns-of-the-month` (paginated list; keyword search on hymn name / derived title)
- `POST /admin/hymns-of-the-month/new`
- `GET /admin/hymns-of-the-month/edit/{id}`
- `PATCH /admin/hymns-of-the-month/edit/{id}`
- `DELETE /admin/hymns-of-the-month`

Factories parse the payload: `isEnabled`, `month` (YYYY-MM), `hymnPsalmName`, optional
base64 `musicSheet`, and a `practiceTracks` array of `{title, base64?|existingPath}`. On
create/edit, run files through `HymnFileStorage`, then persist + enqueue Redis generation.

## Step 8 — Web admin UI (`web/app/admin/hymns-of-the-month/`)
Mirror `web/app/admin/pastors-page/` (list, paginated route, new, edit, delete, search,
role guard, `GetHymnsOfTheMonth.ts`, types, submit actions). Differences from Pastors Page:
- **Month field:** `<input type="month">` → submitted as `YYYY-MM` (replaces title/slug/date).
  No title or slug inputs.
- **Hymn/Psalm name:** textarea.
- **Sheet music upload:** single file input, base64 via existing `FileToBase64.ts`
  (`web/app/admin/messages/FileToBase64.ts`); show current filename when editing.
- **Practice tracks:** repeatable rows — each a title input + audio file input; add/remove
  rows; co-located handlers in the component (per React co-location rule). Existing tracks
  show their current filename; only newly chosen files send base64.
- Sidebar + active-nav: add `hymns-of-the-month` to `web/app/admin/Layout/Sidebar.tsx`
  (gated on `EDIT_HYMNS_OF_THE_MONTH`) and the `activeNav` union in `AdminLayout.tsx`.

## Step 9 — Front-end read cutover
Point the public member pages at the new keys:
- `web/app/members/hymns-of-the-month/GetPageData.ts` → read
  `api-members:hymns_of_the_month:index`.
- `web/app/members/hymns-of-the-month/[slug]/GetPageData.ts` → read
  `api-members:hymns_of_the_month:slug:{slug}`.
- Keep `HymnEntry.ts` and the download route
  (`download/[...filepath]/route.ts`) unchanged — payload shape and file paths are preserved.

(Craft and the new API run side-by-side via the separate `api-members:` key namespace. Craft
CMS as a whole is powered down later, once all sections have been migrated — not per section.)

---

## Verification
No automated test suite in this project — verify via static analysis + manual run
(phpcs, phpstan, eslint, tsc must all pass with zero warnings).

1. **Static:** run the project's phpcs/phpstan (api + auth) and eslint/tsc (web); zero warnings.
2. **Migration:** run the new Phinx migration; confirm `hymns_of_the_month` table shape.
3. **Import:** run `transfer:import:hymns-of-the-month` against local Craft; confirm rows,
   derived slugs match the old Craft slugs (e.g. `january-2024`), and `practice_tracks` JSON
   round-trips into `HymnPracticeTracks`.
4. **Redis:** run the generate command; inspect `api-members:hymns_of_the_month:index` and a
   `:slug:{slug}` key — payload shape matches the current front-end's expectations.
5. **Front-end (public):** load `/members/hymns-of-the-month` and a single hymn page; confirm
   listing, hymn name, sheet-music download, and each practice track stream/download work
   (download route still resolves `{slug}/music` & `{slug}/audio`). Use the `docker exec
   stmark-web touch` trick if the bind-mount misses an edit.
6. **Admin:** as a user with `EDIT_HYMNS_OF_THE_MONTH`, create a new hymn with a month, a
   sheet PDF, and 2 practice tracks; confirm files land at `filesAboveWebroot/{slug}/music`
   and `{slug}/audio`, Redis regenerates, and the member page shows the new hymn. Edit and
   delete; confirm file cleanup and Redis pruning.
7. Confirm the role appears in the user-management UI and gates both the API routes and the
   admin sidebar item.

## Notes / risks
- **Slug exactness is load-bearing.** Deriving `slug` via `Slugify(date->format('F, Y'))`
  must reproduce Craft's existing slugs, because migrated file paths embed the old slug.
  Verify against real Craft data (verification Step 3) before relying on the new keys.
- **File-type validation differs from InternalMessages:** sheet music is a PDF (not MP3) —
  the storage/validation must accept PDF for music and MP3/audio for practice tracks, rather
  than InternalMessages' MP3-only check.
- New uploads keep the existing `{slug}/music` & `{slug}/audio` layout (not InternalMessages'
  `internal-audio/{slug}`) so old and new files share one convention and the existing
  download route needs no change.
