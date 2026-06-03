# Migrate "Men of the Mark" from Craft CMS to the new API + Admin

## Context

"Men of the Mark" (publications) is still authored in Craft CMS. The Next.js
front-end already renders it, reading from Redis keys that **Craft** currently
populates (`publications:men_of_the_mark:index` and
`publications:men_of_the_mark:slug:{slug}`). We are doing the same
strangler-fig migration we did for Messages, Internal Messages, and News: stand
up a first-class feature in the new PHP `api` app (entities, Redis generation,
admin CRUD UI, and a one-shot importer from Craft), write to a **new** Redis
prefix so Craft can keep running untouched, then cut the front-end over to the
new prefix.

The new prefix is **`api-publications:men_of_the_mark`**, giving keys
`api-publications:men_of_the_mark:index` and
`api-publications:men_of_the_mark:slug:{slug}`.

This feature is **simpler than News**: one content field (`body`), no
heading/subheading, no excerpt, no page-builder content, and **no pagination**
(a single flat `:index` list). Do not blindly copy News's paginated Redis
builder — see "Critical divergence" below.

Decisions confirmed with TJ: full scope incl. front-end cutover; fields are
`title, slug, date, enabled, body`; dedicated new role `EDIT_MEN_OF_THE_MARK`;
prefix `api-publications:men_of_the_mark`. No tests (project has no test suite).

## Reference pattern (what we are mirroring)

News is the closest blueprint. Key reference files:

- Backend feature: `api/src/News/` (entities, `NewsRepository`, `Persistence/`, `Admin/`, `Generate/`)
- Redis generation: `api/src/News/Generate/{GenerateNewsPagesForRedis,NewsRedisKey,NewsPagesBuilder,NewsEntryJsonFactory}.php`
- Import command: `api/src/Transfer/News/ImportNewsFromCraftCommand.php`
- Craft transfer endpoint: `craft-cms/src/Transfer/GetTransferNews.php`
- Route/command registration: `api/config/Events/ApplyRoutes.php`, `api/config/Events/ApplyCommands.php`
- Table migration: `api/config/Data/Migrations/20260602000001_create_news_table.php`
- Roles: `api/src/Auth/UserRole.php`, `auth/src/User/UserRole.php`, `api/src/Auth/RequireEditNewsRoleMiddleware.php`
- Web admin: `web/app/admin/news/**`
- Web front-end + repository: `web/app/publications/men-of-the-mark/**` (already exists)

## Critical divergence: Redis output shape must match the existing front-end

The Next front-end's `PublicationEntry` type (`web/app/publications/PublicationEntry.ts`)
is `{ uid, title, slug, publicationDate, bodyHtml }`. The generation code must
write exactly:

- `api-publications:men_of_the_mark:index` → `{ "entries": [ {uid, title, slug, publicationDate, bodyHtml}, ... ] }`
- `api-publications:men_of_the_mark:slug:{slug}` → `{ "entry": {uid, title, slug, publicationDate, bodyHtml} }`

Field mapping in the JSON factory:
- `uid` = `id->toString()`
- `title`, `slug` = as-is
- `publicationDate` = `date->format('Y-m-d H:i:s')` (the RSS route does `new Date(publicationDate)`)
- `bodyHtml` = raw `body` HTML (no excerpt, no typography filters — matches current Craft output)

Gating (mirror News semantics): only `isEnabled` items are written; live items
(`date <= now`) appear in the `:index` list; future-dated enabled items get a
slug-only key so their permalink resolves before they go live. Order the
`:index` entries by `date` DESC.

## Work items

### 1. API — feature module `api/src/MenOfTheMark/`

Mirror `api/src/News/` but with the reduced field set `id, isEnabled, date, title, slug, body`:

- `MenOfTheMarkItem.php` / `NewMenOfTheMarkItem.php` — immutable readonly entities, auto-slug from title when slug empty (reuse the Cocur\Slugify approach in `api/src/News/CreateNewsSlug.php`), `asArray()`, `with*()` builders, self-validation.
- `MenOfTheMarkItems.php` — typed collection with `filter`/`map`/`findById` (drop `sliceToPage` — not paginated).
- `MenOfTheMarkItemResult.php`, `MenOfTheMarkValidation.php`, `CreateMenOfTheMarkSlug.php`.
- `MenOfTheMarkRepository.php` — `create/delete/persist/findAll/findById`, enqueues Redis regen after writes.
- `Persistence/` — `Record`, `Records`, `Transformer`, `FindAll` (ORDER BY date DESC), `FindById`, `Create/`, `Persist/`, `Delete/` (copy News's PDO classes, adjust columns).

### 2. API — Redis generation `api/src/MenOfTheMark/Generate/`

- `MenOfTheMarkRedisKey.php` — namespace `api-publications:men_of_the_mark:`; helpers `index()` → `…:index`, `slug($slug)` → `…:slug:{slug}`, `isSlugKey()`, `allPattern()`.
- `MenOfTheMarkEntryJsonFactory.php` — produces the 5-field entry array described above (no `ComposeContent`/excerpt).
- `GenerateMenOfTheMarkPagesForRedis.php` — filter enabled, split live/future, pipeline write inside a Redis MULTI, prune orphans via an `ExistingRedisKeys` equivalent.
- `MenOfTheMarkIndexBuilder.php` — **new, simpler builder** (replaces `NewsPagesBuilder`): write one `:index` key `{entries: [...]}` from live items (date DESC), write a `:slug:{slug}` key `{entry: {...}}` for every live + future item, delete stale index/slug keys.
- `GenerateMenOfTheMarkPagesForRedisCommand.php` + `EnqueueGenerateMenOfTheMarkPagesForRedis.php` — CLI command + enqueue-with-dedup, mirroring News.

### 3. API — admin CRUD `api/src/MenOfTheMark/Admin/`

Mirror `api/src/News/Admin/` minus heading/subheading. Routes (all gated by the new middleware):
- `GET /admin/men-of-the-mark` (list, search by title, paginated admin list — admin listing can keep pagination; only the *public* Redis output is unpaginated)
- `GET /admin/men-of-the-mark/has-edit-role`
- `POST /admin/men-of-the-mark/new`
- `GET /admin/men-of-the-mark/edit/{id}`
- `PATCH /admin/men-of-the-mark/edit/{id}`
- `DELETE /admin/men-of-the-mark`

### 4. API — auth / role

- Add `case EDIT_MEN_OF_THE_MARK;` to **both** `api/src/Auth/UserRole.php` and `auth/src/User/UserRole.php`.
- New `api/src/Auth/RequireEditMenOfTheMarkRoleMiddleware.php` extending `RequireRoleMiddleware`, returning `UserRole::EDIT_MEN_OF_THE_MARK`.
- The auth user-management UI auto-includes the new role via `UserRole::cases()` (`auth/src/ManageUsers/RoleCheckboxesFactory.php`) — no change needed there.

### 5. API — DB migration

- `api/config/Data/Migrations/<timestamp>_create_men_of_the_mark_table.php` — table `men_of_the_mark` with `id` (uuid/char36, PK), `is_enabled`, `date`, `title`, `slug`, `body`. Copy the News migration and drop heading/subheading.

### 6. API — registration

- Register the 6 admin actions in `api/config/Events/ApplyRoutes.php`.
- Register the generate command, enqueue, and import command in `api/config/Events/ApplyCommands.php`.

### 7. Craft — transfer endpoint

- New `craft-cms/src/Transfer/GetTransferMenOfTheMark.php` at `GET /transfer/men-of-the-mark`. Query `section('menOfTheMark')`, `status(null)`. Per entry return `{ id: uid, date: postDate→US/Central→'Y-m-d H:i:s', title, slug, enabled: status !== DISABLED, body }`. `body` comes straight from the Redactor field via `GenericHandler::getString(element, 'body')` — **no** entryBuilder/matrix handling (unlike News).
- Register the route in `craft-cms/config/slim/routes.php`.

### 8. API — import command

- New `api/src/Transfer/MenOfTheMark/ImportMenOfTheMarkFromCraftCommand.php`, command `transfer:import:men-of-the-mark`. Mirror `ImportNewsFromCraftCommand`: GET the Craft endpoint, parse, create new or persist-if-changed by UUID, preserving Craft `id` and `slug`. Reduced field set (no heading/subheading).

### 9. Web — admin UI `web/app/admin/men-of-the-mark/`

Mirror `web/app/admin/news/**` with the reduced form (title, slug, date, enabled toggle, rich-text body — drop heading/subheading). Includes list/new/edit/delete pages, create-edit shared form, parse-form-data, title+slug auto-slug fields, and `HasEditMenOfTheMarkRoleGuard/` (guard + `GetHasEditMenOfTheMarkRole.ts` hitting `/admin/men-of-the-mark/has-edit-role`). All API calls target the `/admin/men-of-the-mark` endpoints above.

### 10. Web — navigation

- `web/app/admin/Layout/Sidebar.tsx` — add a nav item gated by `userinfo.roles.includes('EDIT_MEN_OF_THE_MARK')`, `href: '/admin/men-of-the-mark'`, `current: activeNav === 'menOfTheMark'`.
- `web/app/admin/Layout/AdminLayout.tsx` — add `'menOfTheMark'` to the `activeNav` union (and the matching union in `Sidebar.tsx`).

### 11. Web — front-end cutover (last)

Only after the API is generating the new keys and the importer has run: change the two hardcoded Redis keys to the new prefix.
- `web/app/publications/men-of-the-mark/repository/FindAllMenOfTheMarkEntries.ts` — `publications:men_of_the_mark:index` → `api-publications:men_of_the_mark:index`
- `web/app/publications/men-of-the-mark/repository/FindMenOfTheMarkBySlug.ts` — `publications:men_of_the_mark:slug:${slug}` → `api-publications:men_of_the_mark:slug:${slug}`

No type/shape changes needed — the new API output matches `PublicationEntry` exactly.

## Verification

No automated tests (project has none). Verify via tooling + manual:

1. **Static checks (zero warnings required):**
   - API: `phpcs` + `phpstan` over `api/`.
   - Craft: `phpcs` + `phpstan` over the new transfer file.
   - Web: `eslint` + `tsc`.
2. **Migration:** run the new DB migration; confirm `men_of_the_mark` table exists.
3. **Import:** run `transfer:import:men-of-the-mark`; confirm rows land with Craft UUIDs/slugs preserved.
4. **Redis generation:** run the generate command; inspect Redis:
   - `GET api-publications:men_of_the_mark:index` → `{entries:[…]}` with 5-field entries, date DESC.
   - `GET api-publications:men_of_the_mark:slug:{some-slug}` → `{entry:{…}}`.
   - Confirm the JSON matches `web/app/publications/PublicationEntry.ts` field-for-field.
5. **Admin UI:** as a user with `EDIT_MEN_OF_THE_MARK`, confirm the sidebar item appears and create/edit/delete works; confirm a user without the role doesn't see it and is blocked by the middleware.
6. **Front-end cutover:** after switching the two keys, load `/publications/men-of-the-mark` (listing), a detail page, and `/publications/men-of-the-mark/rss`; confirm identical rendering to before the cutover.
7. **Craft untouched:** confirm Craft still writes its old `publications:men_of_the_mark:*` keys (we never removed them) — old prefix can be cleaned up later.

## Out of scope / follow-ups

- Removing Craft's Men-of-the-Mark Redis generation and the old `publications:men_of_the_mark:*` keys (do after the cutover is confirmed stable).
- Decommissioning the Craft section itself.
