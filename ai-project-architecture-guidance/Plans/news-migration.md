# Migrate News from Craft CMS to the new API + Admin

## Context

News posts currently live in Craft CMS, rendered through Craft's old "page builder" (an `entryBuilder` Matrix with a `basicEntryBlock`). In practice the page builder is unused: **640 of 646** News entries are a single `basicEntryBlock`, and the only fields that matter are **Heading**, **Subheading**, and the rich-text **Body**. We want News managed in the new custom API + React admin, following the exact pattern already established for **Messages** and **Internal Messages**, and then cut the public Next.js front-end over to a new Redis key prefix (`api-news:`) so Craft can keep running until we flip the switch.

Verified facts driving this plan (queried against the dev DB and traced through code):

- **Content model**: only Heading / Subheading / Body are used. New admin fields: Title, Slug, Date, Enabled, Heading, Subheading, Body (rich text).
- **Public front-end already renders Heading + Subheading** — but as `<h2>`/`<h3>` baked **inside the `content` HTML string** (see `craft-cms/src/Http/PageBuilder/BlockResponse/BasicEntryBlock/BasicEntryBlock.twig`), not as separate payload fields. The new API must reproduce that same composed HTML so the public site needs no template changes.
- **Date gating**: an enabled post's permalink (slug key) must exist immediately (so social posts can pull from the URL pre-launch), but it must not appear in the listing until its Date is reached. A 5-minute scheduled job already exists (`api/config/ScheduleFactory.php`) and is the mechanism for rolling future-dated posts into listings.
- **Images**: only ~6 of 646 posts have images (3 inline `<img>` in body, 3 using a separate `imageEntryBlock`). Decision: **no editor image support**; preserve body HTML verbatim so inline-image posts keep rendering; the 3 `imageEntryBlock` posts (`2011-fall-festival`, `sunday-sessions-flannery-o-connor`, `newsletter-may-12-2024`) lose their image block and get flagged for manual cleanup.
- **OpenGraph meta**: skipped for now.

## Reference implementations to mirror

This is overwhelmingly a "copy the Messages slice, simplify it" job. Mirror these directories/files:

- API feature slice: `api/src/Messages/` (and its `Persistence/`, `Generate/`, `Search/`, admin actions)
- API migration: `api/config/Data/Migrations/20260325231457_create_messages_table.php`
- Redis generation: `api/src/Messages/Generate/GenerateMessagesPagesForRedis.php`, `MessagesRedisKey.php`, `*PagesBuilder.php`, `EnqueueGenerateMessagesPagesForRedis.php`
- Schedule registration: `api/config/ScheduleFactory.php`
- Routes / commands registration: `api/config/Events/ApplyRoutes.php`, `api/config/Events/ApplyCommands.php`
- Craft transfer endpoint: `craft-cms/src/Transfer/GetTransferMessages.php` (+ route in `craft-cms/config/slim/routes.php`)
- API import command: `api/src/Transfer/Messages/ImportMessagesFromCraftCommand.php`
- Web admin slice: `web/app/admin/messages/` (list + new + edit), `web/app/admin/Layout/Sidebar.tsx`, `web/app/admin/Forms/RichText.tsx`
- Public consumption (cutover target): `web/app/news/repository/FindNewsItemsByPage.ts`, `FindNewsItemBySlug.ts`, `NewsItem.ts`

Naming for the new slice: feature dir `News/`, entity `NewsItem`, collection `NewsItems`, input `NewNewsItem`, plus `NewsItemResult`, `NewsItemValidation`, `CreateNewsSlug`, `NewsRepository`.

---

## Part 1 — API: News feature slice (`api/src/News/`)

**Entity & collection** (mirror `Message.php` / `Messages.php`):
- `NewsItem` (readonly, immutable `with*()` builders, validation in constructor): `id`, `isEnabled`, `date` (US/Central), `title`, `slug`, `heading`, `subheading`, `body` (HTML string).
- `NewNewsItem` (creation DTO), `NewsItems` (typed collection with newest-first sort + pagination helpers), `NewsItemResult`.
- `NewsItemValidation`: `title` required; `body` required. (No audio/speaker/series — drop all of that from the Messages template.)
- `CreateNewsSlug`: slugify **title only** via `cocur/slugify` (mirror `CreateMessageSlug.php` but **no date prefix**). Used only when slug is null/unset.

**Persistence** (`api/src/News/Persistence/`, mirror `Messages/Persistence/`):
- `NewsItemRecord`, `NewsItemRecords`, `Transformer` (record ↔ entity; no Profile/Series lazy-loading needed).
- `FindAll`, `FindById`, plus `Create/`, `Persist/`, `Delete/` transaction + PDO classes.
- `NewsRepository`: `create`, `persist`, `delete`, `findAll`, `findById`.

**Migration** (`api/config/Data/Migrations/<timestamp>_create_news_table.php`, Phinx):
Table `news`: `id` (uuid, PK), `enabled` (bool, default true), `date` (datetime, indexed), `title` (indexed), `slug` (indexed), `heading` (string, nullable), `subheading` (string, nullable), `body` (text).

**Redis generation** (`api/src/News/Generate/`, mirror `Messages/Generate/`):
- `NewsRedisKey`: prefix **`api-news:`** with `slug:{slug}` and `page:{n}` keys (matches what the public FE reads, just a new prefix).
- `ComposeNewsContent`: builds the `content` HTML — Heading as `<h2>` + Subheading as `<h3>` using the **exact markup/classes from `BasicEntryBlock.twig`**, followed by the body HTML. This is what makes the public detail page render unchanged.
- Body-only extraction for `bodyOnlyContent` (used by listing cards + excerpt). Match the current output of Craft's `ExtractBodyContent` / `CompileResponse` (trace `craft-cms/src/Http/Response/News/GenerateNewsTypePagesForRedis.php`). `excerpt` = `bodyOnlyContent` truncated to 300 chars.
- `GenerateNewsPagesForRedis::generate()`:
  - `findAll()` → filter `isEnabled`.
  - **slug keys**: write **all enabled** items (any date) → permalink always live. Payload: `{ entry: { uid, title, slug, excerpt, content, bodyOnlyContent, readableDate, postDate } }`.
  - **page keys**: only items with `date <= now` (US/Central), newest-first, paginate **12/page** (matches Craft). Payload mirrors existing: `{ currentPage, perPage, totalResults, totalPages, pagesArray:[{isActive,label,target}], prevPageLink, nextPageLink, firstPageLink, lastPageLink, entries:[...] }`.
  - Pipeline writes + orphan cleanup (`KEYS api-news:*` then delete keys not re-written), exactly like Messages.
- `EnqueueGenerateNewsPagesForRedis` + a `GenerateNewsPagesForRedis` job handle; enqueue on create/update/delete.

**Schedule**: add an `EnqueueGenerateNewsPagesForRedis` item to the collection in `api/config/ScheduleFactory.php` (5-min cadence) so future-dated posts roll into listings automatically.

**Admin actions** (`api/src/News/Admin/`, mirror Messages actions; register in `api/config/Events/ApplyRoutes.php`):
- `POST /admin/news/new`, `GET /admin/news` (paginated + keyword), `GET /admin/news/{id}`, `POST /admin/news/{id}`, `POST /admin/news/delete`.
- New middleware `RequireEditNewsRoleMiddleware` + a corresponding edit-news role, following exactly how `RequireEditMessagesRoleMiddleware` and its role enum value are defined/registered. (Skip Elasticsearch — News admin list keyword filter can run off the repository like the simpler sections; do not port `Messages/Search/`.)

---

## Part 2 — Web admin (`web/app/admin/news/`)

Mirror `web/app/admin/messages/` (list + `new/` + `edit/[newsId]/`), simplified to the News fields:
- `page.tsx`, `NewsPage.tsx`, `NewsPageClientSide.tsx`, `GetNews.ts`, `AdminNewsPageData.ts`, `NewsItem.ts`.
- `CreateEditNewsPage.tsx` form fields: **Title**, **Slug**, **Date**, **Enabled** toggle, **Heading**, **Subheading**, **Body** via the existing `RichText` component (`web/app/admin/Forms/RichText.tsx` — CKEditor, HTML output, the same one Profiles uses; reuse its config as-is, no image plugin).
- `CreateEditNewsParseFormData.ts`, `CreateEditNewsValues.ts`, `CreateEditNewsSubmitActionState.ts`, plus `new/` and `edit/[newsId]/` server actions and `GetEditNews.ts`.
- **Slug auto-fill**: client-side, generate slug from Title only while the Slug field is untouched/empty; stop auto-filling once the user edits Slug manually. (New posts only; on edit, leave the existing slug.)
- Nav: add a News item to `web/app/admin/Layout/Sidebar.tsx` gated on the edit-news role, and extend the `activeNav` union in `web/app/admin/Layout/AdminLayout.tsx` with `'news'`.

---

## Part 3 — Migration from Craft

**Craft transfer endpoint** (`craft-cms/src/Transfer/GetTransferNews.php`, route `GET /transfer/news` in `craft-cms/config/slim/routes.php`; mirror `GetTransferMessages.php`):
- Query section `news` (sectionId 5). For each entry return: `uid`, `date` (`Y-m-d H:i:s`, US/Central), `title`, `slug`, `enabled` (status), `heading`, `subheading`, `body`.
- Source from the `entryBuilder` Matrix `basicEntryBlock` blocks. For the 6 multi-block entries, concatenate blocks in `sortOrder` (heading/subheading/body each). Preserve body HTML **verbatim** (keeps the 3 inline-image posts intact). `imageEntryBlock` content is not exported.

**API import command** (`api/src/Transfer/News/ImportNewsFromCraftCommand.php`, register `transfer:import:news` in `api/config/Events/ApplyCommands.php`; mirror `ImportMessagesFromCraftCommand.php`):
- GET Craft `/transfer/news` (Guzzle, base URL from `RuntimeConfigOptions::APP_API_URL`).
- Upsert by `uid`: if absent, create `NewNewsItem` **preserving the Craft slug** (critical — existing permalinks must not change); if present, update changed fields. Enqueue Redis generation after the run.

**Manual cleanup (post-import)**: re-check the 3 `imageEntryBlock` posts (`2011-fall-festival`, `sunday-sessions-flannery-o-connor`, `newsletter-may-12-2024`) and the recent 4-block "Newsletter" posts (`newsletter-may-10-2026`, `newsletter-may-17-2026`, `newsletter-may-24-2026`) by hand.

---

## Part 4 — Front-end cutover (do last, after verifying generation)

Switch the public read prefix from Craft's `news:` to `api-news:` in `web/app/news/repository/FindNewsItemsByPage.ts` and `FindNewsItemBySlug.ts` (change the `sectionHandle`/prefix passed in). The `NewsItem` interface and all templates stay unchanged because the new payload matches the existing shape. This is the single switch that moves the live site onto the new system.

---

## Verification

1. **Build quality**: `phpcs`, `phpstan`, `eslint`, `tsc` all zero warnings.
2. **Migration**: `docker exec stmark-api php cli migrate:up`; confirm `news` table.
3. **Admin round-trip**: create a News post in `/admin/news`; confirm `api-news:slug:<slug>` and `api-news:page:1` exist (`docker exec stmark-redis redis-cli KEYS 'api-news:*'`) and JSON matches the expected shape.
4. **Date gating**: create an enabled post dated in the future → slug key present, absent from `page:1`; run `php cli schedule:run` (or wait a cycle) after its date and confirm it joins the listing.
5. **Import**: `docker exec stmark-api php cli transfer:import:news`; expect ~646 rows; spot-check that several slugs exactly match Craft and that the 3 inline-image posts render correctly.
6. **Cutover smoke test**: flip the prefix, load `/news` and a permalink; confirm Heading/Subheading render above the body identically to today.
