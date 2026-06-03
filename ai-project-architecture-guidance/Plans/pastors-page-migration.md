# Migrate the Pastors Page from Craft CMS to the new API + Admin

## Context

The "Pastor's Page" (`pastorsPage`) is a Craft CMS **channel** (URL
`pastors-page/{slug}`, 12 entries per listing page) still authored in Craft. Its
Craft entry type carries hero fields, a `entryBuilder` page-builder matrix, and
SEO fields — but, exactly like News, **the Next.js front-end consumes none of
that structure directly.** The public `web/app/pastors-page/` routes already
*reuse the News front-end wholesale*:

- `web/app/pastors-page/page.tsx` renders `<NewsIndexPage sectionHandle="pastorsPage" baseUri="/pastors-page" … />`
- `web/app/pastors-page/[slug]/page.tsx` calls `FindNewsItemBySlug('pastorsPage', slug)` and renders `entry.content`
- `web/app/pastors-page/page/[pageNum]/page.tsx` and `…/rss/route.ts` likewise pass `'pastorsPage'` into the shared News repository

The repository builds keys as `${sectionHandle}:slug:{slug}` and
`${sectionHandle}:page:{n}`, reading the same 8-field payload shape as News
(`uid, title, slug, excerpt, content, bodyOnlyContent, readableDate, postDate`).
Craft currently writes those keys under the **`pastorsPage:`** prefix via
`craft-cms/src/Http/Response/News/GeneratePastorsPagesForRedis.php` (delegating
to the shared `GenerateNewsTypePagesForRedis`).

This is the same strangler-fig migration we did for **News**, **Men of the
Mark**, and **Messages**: stand up a first-class feature in the new PHP `api`
app (entity, persistence, Redis generation, admin CRUD, a one-shot Craft
importer), write to a **new** Redis prefix so Craft keeps running untouched,
then cut the front-end over by flipping one string.

**Decisions confirmed with TJ:**
- **Fields mirror News exactly:** Title, Slug, Date, Enabled, Heading,
  Subheading, Body (rich text). Drop hero & SEO. Heading/Subheading are composed
  into the `content` HTML, identical to News.
- **New Redis prefix `api-pastorsPage:`** (cutover = change the `sectionHandle`
  string in the front-end files).
- **Dedicated role `EDIT_PASTORS_PAGE`.**
- **Full scope including the front-end cutover.**
- No tests (project has no test suite) — verify via static analysis + manual.

## Reference pattern (what we are mirroring)

News is a near-perfect blueprint — this is overwhelmingly a "copy the News
slice, rename, change the prefix" job. Key reference files:

- API feature slice: `api/src/News/` (entity, `NewsRepository`, `Persistence/`, `Admin/`, `Generate/`)
- Redis generation: `api/src/News/Generate/{GenerateNewsPagesForRedis,NewsRedisKey,NewsPagesBuilder,NewsEntryJsonFactory,ComposeNewsContent,ExistingRedisKeys,EnqueueGenerateNewsPagesForRedis,GenerateNewsPagesForRedisCommand}.php`
- Table migration: `api/config/Data/Migrations/20260602000001_create_news_table.php`
- Registration: `api/config/Events/ApplyRoutes.php`, `api/config/Events/ApplyCommands.php`, `api/config/ScheduleFactory.php`
- Roles: `api/src/Auth/UserRole.php`, `auth/src/User/UserRole.php`, `api/src/Auth/RequireEditNewsRoleMiddleware.php`
- Craft transfer: `craft-cms/src/Transfer/GetTransferNews.php` (+ route in `craft-cms/config/slim/routes.php`)
- API import: `api/src/Transfer/News/ImportNewsFromCraftCommand.php`
- Web admin slice: `web/app/admin/news/**`
- Web front-end (cutover target): `web/app/pastors-page/**` (already exists; reuses `web/app/news/repository/**`)

Naming for the new slice: feature dir `PastorsPage/`, entity `PastorsPageItem`,
collection `PastorsPageItems`, input `NewPastorsPageItem`, plus
`PastorsPageItemResult`, `PastorsPageItemValidation`, `CreatePastorsPageSlug`,
`PastorsPageRepository`.

---

## Part 1 — API: PastorsPage feature slice (`api/src/PastorsPage/`)

Mirror `api/src/News/` field-for-field (same Title/Slug/Date/Enabled/Heading/
Subheading/Body model).

**Entity & collection** (mirror `NewsItem.php` / `NewNewsItem.php` / `NewsItems.php`):
- `PastorsPageItem` (readonly, immutable `with*()` builders, self-validation, date normalized to US/Central, `asArray()` + `JsonSerializable`): `id`, `isEnabled`, `date`, `title`, `slug`, `heading`, `subheading`, `body`.
- `NewPastorsPageItem` (creation DTO, trailing optional `id` for Craft import).
- `PastorsPageItems` (typed collection: `sliceToPage`, `map`, `filter`, `findById`, `asArray`).
- `PastorsPageItemResult` (wrapper).
- `PastorsPageItemValidation`: `title` required, `body` required.
- `CreatePastorsPageSlug`: slugify **title only** via `cocur/slugify` (mirror `CreateNewsSlug` — no date prefix). Used only when slug is null/empty.

**Persistence** (`api/src/PastorsPage/Persistence/`, mirror `News/Persistence/`):
- `PastorsPageItemRecord` (`TABLE_NAME = 'pastors_page'`), `PastorsPageItemRecords`, `Transformer` (record ↔ entity).
- `FindAll` (ORDER BY date DESC), `FindById`.
- `Create/` (`CreatePastorsPageItem` transaction wrapper + `CreatePastorsPageItemInPdo`), `Persist/`, `Delete/` — copy the News PDO classes, adjust columns; create/persist/delete enqueue Redis regen.
- `PastorsPageRepository`: `create`, `delete`, `persist`, `findAll`, `findById`.

**Migration** (`api/config/Data/Migrations/<timestamp>_create_pastors_page_table.php`, Phinx):
Copy `create_news_table`. Table `pastors_page`: `id` (uuid, PK), `enabled` (bool, default true), `date` (datetime), `title`, `slug`, `heading`, `subheading` (strings), `body` (text). Indexes on `enabled`, `date`, `title`, `slug`.

**Redis generation** (`api/src/PastorsPage/Generate/`, mirror `News/Generate/`):
- `PastorsPageRedisKey`: prefix **`api-pastorsPage:`** with `page:{n}` and `slug:{slug}` keys, `isPageKey`/`isSlugKey`/`allPattern`.
- `ComposePastorsPageContent`: build `content` HTML — Heading as `<h2>` + Subheading as `<h3>` (same markup/classes as `ComposeNewsContent`) followed by body HTML, so the public detail page renders unchanged.
- `PastorsPageEntryJsonFactory`: produce the 8-field payload (`uid, title, slug, excerpt`[300-char word-boundary truncation], `content, bodyOnlyContent, readableDate`[`F jS, Y`], `postDate`[RFC2822]) — identical to `NewsEntryJsonFactory`.
- `PastorsPageBuilder` (mirror `NewsPagesBuilder`): paginate live items **12/page**, write page keys + slug keys for live items, prune orphans via `ExistingRedisKeys`. **Unlike News, do NOT write slug-only keys for future-dated items** — a Pastor's Page permalink must not exist before the entry's date.
- `GeneratePastorsPageForRedis` (`PER_PAGE = 12`): `findAll()` → filter enabled → keep only live (`date <= now`) → build inside a Redis pipeline. Future-dated items get no key at all.
  - **slug keys**: only `date <= now` (no permalink until the date arrives).
  - **page keys**: only `date <= now`, newest-first, 12/page.
- `GeneratePastorsPageForRedisCommand` (CLI `pastors-page:generate-redis-pages`) + `EnqueueGeneratePastorsPageForRedis` (enqueue-with-dedup); enqueue on create/update/delete.

**Schedule**: add an `EnqueueGeneratePastorsPageForRedis` item (5-min cadence) to the collection in `api/config/ScheduleFactory.php` so future-dated entries roll into listings automatically.

**Admin actions** (`api/src/PastorsPage/Admin/`, mirror `News/Admin/`; register in `api/config/Events/ApplyRoutes.php`):
- `GET /admin/pastors-page/has-edit-pastors-page-role`
- `GET /admin/pastors-page` (paginated list + keyword filter on title/heading/subheading, off the repository — no Elasticsearch)
- `POST /admin/pastors-page/new`
- `GET /admin/pastors-page/edit/{id}`
- `PATCH /admin/pastors-page/edit/{id}`
- `DELETE /admin/pastors-page`
- All gated by a new `RequireEditPastorsPageRoleMiddleware` returning `UserRole::EDIT_PASTORS_PAGE`.
- Include `PaginatedPastorsPage`, `NewPastorsPageItemFactory`, and the edit `PastorsPageItemFactory`/responder, mirroring News.

---

## Part 2 — API: auth / role

- Add `case EDIT_PASTORS_PAGE;` to **both** `api/src/Auth/UserRole.php` and `auth/src/User/UserRole.php`.
- New `api/src/Auth/RequireEditPastorsPageRoleMiddleware.php` extending `RequireRoleMiddleware`, returning `UserRole::EDIT_PASTORS_PAGE`.
- User-management UI auto-includes the new role via `UserRole::cases()` — no change needed.

---

## Part 3 — Web admin (`web/app/admin/pastors-page/`)

Mirror `web/app/admin/news/**` with the same fields (Title, Slug, Date, Enabled toggle, Heading, Subheading, Body via the existing `web/app/admin/Forms/RichText.tsx` CKEditor — reuse config as-is, no image plugin):
- List: `page.tsx`, `page/[pageNum]/page.tsx`, `PastorsPagePage.tsx`, `PastorsPagePageClientSide.tsx`, `PastorsPageSearchForm.tsx`, `GetPastorsPage.ts`, `AdminPastorsPagePageData.ts`, `PastorsPageItem.ts`.
- Create/edit shared: `CreateEditPastorsPagePage.tsx`, `PastorsPageTitleSlugFields.tsx`, `CreateEditPastorsPageValues.ts`, `CreateEditPastorsPageParseFormData.ts`, `CreateEditPastorsPageSubmitActionState.ts`.
- `new/` and `edit/[id]/` route trees with their server actions (`Create…SubmitFormAction.ts`, `Edit…SubmitFormAction.ts`, `GetEditPastorsPage.ts`).
- Delete: `SubmitDeletePastorsPageActionState.ts`, `SubmitDeletePastorsPageFormAction.ts`.
- Role guard: `HasEditPastorsPageRoleGuard/{HasEditPastorsPageRoleGuard.tsx,GetHasEditPastorsPageRole.ts}` hitting `/admin/pastors-page/has-edit-pastors-page-role`.
- **Slug auto-fill**: client-side from Title only while Slug is untouched/empty; stop once the user edits Slug (new items only; leave existing slug on edit).
- Nav: add a Pastor's Page item to `web/app/admin/Layout/Sidebar.tsx` gated on `roles.includes('EDIT_PASTORS_PAGE')`, `href:'/admin/pastors-page'`; extend the `activeNav` union in `web/app/admin/Layout/AdminLayout.tsx` (and `Sidebar.tsx`) with `'pastorsPage'`.

---

## Part 4 — Migration from Craft

**Craft transfer endpoint** (`craft-cms/src/Transfer/GetTransferPastorsPage.php`, route `GET /transfer/pastors-page` in `craft-cms/config/slim/routes.php`; mirror `GetTransferNews.php`):
- Query section `pastorsPage`, `status(null)` (all incl. disabled/future). Per entry return `uid`, `date` (`Y-m-d H:i:s`, US/Central), `title`, `slug`, `enabled`, `heading`, `subheading`, `body`.
- Source heading/subheading/body from the `entryBuilder` matrix `basicEntryBlock` blocks via the same handlers News uses (`ExtractBodyContent::fromElementWithEntryBuilder()`); preserve body HTML verbatim. (Verify during implementation whether pastors entries actually use `basicEntryBlock` heading/subheading — they should, since they share the entry-type layout; if any use other block types, flag for manual cleanup like News did.)

**API import command** (`api/src/Transfer/PastorsPage/ImportPastorsPageFromCraftCommand.php`, register `transfer:import:pastors-page` in `api/config/Events/ApplyCommands.php`; mirror `ImportNewsFromCraftCommand.php`):
- GET Craft `/transfer/pastors-page` (Guzzle, base URL from `RuntimeConfigOptions::APP_API_URL`).
- Upsert by `uid`: if absent, create `NewPastorsPageItem` **preserving the Craft slug and UUID** (permalinks must not change); if present, persist changed fields. Enqueue Redis generation after the run.

---

## Part 5 — Front-end cutover (do last, after verifying generation)

Flip the `sectionHandle` from Craft's `pastorsPage` to the new `api-pastorsPage`
in the four `web/app/pastors-page/` files — no shape/type changes needed because
the new payload matches the existing News shape:
- `web/app/pastors-page/page.tsx` — `sectionHandle="api-pastorsPage"`
- `web/app/pastors-page/page/[pageNum]/page.tsx` — `sectionHandle` prop
- `web/app/pastors-page/[slug]/page.tsx` — both `FindNewsItemBySlug('api-pastorsPage', slug)` calls (metadata + page)
- `web/app/pastors-page/rss/route.ts` — `FindAllNewsItems('api-pastorsPage')`

This single switch moves the live page onto the new system.

---

## Verification

No automated tests (project has none). Verify via tooling + manual:

1. **Static checks (zero warnings required):** API `phpcs` + `phpstan`; Craft `phpcs` + `phpstan` over the new transfer file; Web `eslint` + `tsc`.
2. **Migration:** `docker exec stmark-api php cli migrate:up`; confirm `pastors_page` table.
3. **Admin round-trip:** as a user with `EDIT_PASTORS_PAGE`, confirm the sidebar item appears and create/edit/delete works; a user without the role is blocked by the middleware. Confirm `api-pastorsPage:slug:<slug>` and `api-pastorsPage:page:1` exist (`docker exec stmark-redis redis-cli KEYS 'api-pastorsPage:*'`) with the expected JSON shape.
4. **Date gating:** create an enabled future-dated entry → **no** slug key and absent from `page:1` (unlike News, the permalink must not exist yet); run `php cli schedule:run` (or wait a cycle) after its date and confirm both the slug key and listing entry appear.
5. **Import:** `docker exec stmark-api php cli transfer:import:pastors-page`; confirm rows land with Craft UUIDs/slugs preserved; spot-check several slugs match Craft and content renders correctly.
6. **Cutover smoke test:** flip the `sectionHandle`, load `/pastors-page`, a permalink, and `/pastors-page/rss`; confirm identical rendering to today (Heading/Subheading above body).
7. **Craft untouched:** confirm Craft still writes its old `pastorsPage:*` keys — old prefix can be cleaned up later.

## Out of scope / follow-ups

- Removing Craft's pastors-page Redis generation and the old `pastorsPage:*` keys (after cutover is confirmed stable).
- Decommissioning the Craft `pastorsPage` section.
