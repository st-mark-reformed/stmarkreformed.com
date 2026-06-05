# Plan: Move the Mailing List (IMAP Forwarding) Feature from Craft to the API

## Context

The mailing-list IMAP check is believed to be the **last remaining piece of functionality still living in the legacy Craft CMS app** (`craft-cms/`). Migrating it lets us finish decommissioning Craft.

**What it does today (confirmed in `craft-cms/src/MailingLists/`):** a scheduled task connects to one or more IMAP mailboxes (one per mailing list), and for every message in the INBOX it forwards the message to that list's subscribers, then deletes it from the mailbox. Config (IMAP credentials, list address, subscribers) lives in an admin-editable Craft **global set** (a SuperTable with one block per list). It runs on `Frequency::ALWAYS` (every scheduler tick) using barbushin's `php-imap` library (which depends on the now-deprecated `ext-imap`).

**Outcome:** the same behavior, rebuilt natively in the `api/` app following the existing **Resources** feature as the structural template, with a Next.js admin UI for managing lists + subscribers, using a pure-PHP IMAP client so no Docker image surgery is needed.

### Locked decisions
| Decision | Choice |
|---|---|
| Config storage | **Full admin UI + DB** (mirror the Resources feature) |
| IMAP library | **`webklex/php-imap`** (pure PHP, **no `ext-imap`, no Dockerfile change**) |
| Schedule frequency | **`Frequency::ALWAYS`** (mirror Craft) |
| Data migration | **Re-enter by hand** in the new admin UI — no Craft transfer endpoint / import command |

### Exact forwarding rules to preserve (from `craft-cms/src/MailingLists/IncomingMailHandler.php`)
- **From** = the system from-address (NOT the original sender), but **preserve the original sender's display name**.
- **To** header = the list address (decorative only).
- **Subject** = incoming subject.
- **Bcc** = every subscriber EXCEPT: the original sender, anyone in the incoming `To`, anyone in the incoming `Cc` (loop prevention / dedup).
- **Reply-To**: if the sender's address **is** a subscriber (internal) → Reply-To = list address (replies go to the whole list). If sender is **not** a subscriber (external) → Reply-To = original sender's address.
- **Attachments**: if an attachment is an inline image referenced via `src="cid:<id>"` in the HTML body → embed it and rewrite the cid reference; otherwise attach as a normal file.
- Set text and/or HTML body if present.
- **On send failure**: move the message to **Drafts** and throw. **On success**: delete the message.

---

## Reference template: the `Resources` feature

Mirror these, file-for-file, swapping "Resource" → "MailingList":
- Domain/persistence: `api/src/Resources/` (`ResourceItem`, `NewResourceItem`, `ResourceItems`, `ResourcesRepository`, `Persistence/{Create,Persist,Delete,FindAll,FindById,Record,Transformer}`). PDO + hand-written SQL, `PDO::FETCH_CLASS` hydration. **Most classes autowire — no DI binding needed.**
- Admin endpoints: `api/src/Resources/Admin/` — each Action has `static applyRoute(ApplyRoutesEvent)` + `__invoke(ServerRequestInterface): ResponseInterface`, guarded by a `RequireEdit<Feature>RoleMiddleware`.
- Scheduled job: `api/src/Resources/Generate/` — Enqueue (dedups by job handle) → queue Job (`JOB_HANDLE`/`JOB_NAME` consts + worker method) → CLI Command (`register()` + `__invoke(): int`). The scheduler **enqueues**; the queue consumer runs the work.
- Email sending: `api/src/Contact/SendEmail.php` — Symfony Mailer (`MailerInterface`, `Mime\Email`, `Mime\Address`) + `Config\SystemFromAddress`.
- Web UI: `web/app/admin/resources/` — list / new / edit pages, `Has<Role>Guard`, fetchers, form value types, and the repeating-row `ResourceDownloadsField.tsx` (model `SubscribersField.tsx` on it).

Three registration points: `api/config/Events/ApplyRoutes.php`, `api/config/Events/ApplyCommands.php`, `api/config/ScheduleFactory.php`.

---

## Implementation

### 1. Database (Phinx migration)
`api/config/Data/Migrations/20260605000001_create_mailing_lists_table.php` (class `CreateMailingListsTable`, mirror `CreateResourcesTable` style). **Two tables** — a separate subscribers table (not a JSON column), because subscribers are human-curated, repeating records the admin UI edits as rows and the handler dedups against by email.

`mailing_lists`: `id` (UUID PK), `list_name`, `list_address`, `imap_server`, `imap_port` (INT, default 993), `connection_type` (STRING, default `ssl`), `username`, `password` (STRING). Indexes on `list_name`, `list_address`.

`mailing_list_subscribers`: `id` (UUID PK), `mailing_list_id` (UUID, FK → `mailing_lists.id` `ON DELETE CASCADE`), `name`, `email_address`. Indexes on `mailing_list_id`, `email_address`.

**Password storage:** plaintext (matches the Craft precedent; the app has no encryption helper, and IMAP login needs a reversible value — same posture as the existing plaintext `SMTP_PASSWORD` env). Keep the password **out of the list-view JSON**; only return it on the single edit fetch (authenticated, admin-only, over TLS). Call this out in the PR.

### 2. Domain + persistence (`api/src/MailingLists/`)
- `ConnectionType.php` — backed enum `Ssl='ssl' | Tls='tls' | None='none'` with a safe `fromString()` (default `Ssl`). Replaces Craft's magic string.
- `Subscriber.php` (id + name + emailAddress, readonly, `JsonSerializable`), `Subscribers.php` (typed collection: `map`, `filter`, `count`, `hasEmailAddress()`, `jsonSerialize`).
- `MailingList.php` (readonly entity incl. `Subscribers`, `with*` mutators, `asArray()` + `asArrayWithoutPassword()`), `NewMailingList.php`, `MailingLists.php` (collection: `map/filter/findById/jsonSerialize`), `MailingListValidation.php`, `MailingListResult.php`.
- `Persistence/`: `MailingListRecord(s)`, `SubscriberRecord(s)`, `Transformer` (`ConnectionType::fromString()`; compose subscribers per list), `FindAll` (one lists query + one subscribers query grouped in PHP — avoid N+1), `FindById`, `Create/CreateMailingList(+InPdo)`, `Persist/PersistMailingList(+ToPdo)` (UPDATE list + **delete-all & re-insert** subscribers in one transaction), `Delete/DeleteMailingList` (subscribers cascade).
- `MailingListsRepository.php` — mirror `ResourcesRepository` (`create/delete/persist/findAll/findById`). Used by both the admin actions and the scheduled check. **No Redis page generation** (mailing lists don't produce static pages).

### 3. The IMAP check (`api/src/MailingLists/Check/`)
Wrap `webklex/php-imap` at the boundary so the forwarding logic operates on our own value objects and stays type-clean for phpstan.
- `ImapClientFactory.php` — builds a connected client from a `MailingList` via `Webklex\PHPIMAP\ClientManager` (`host`, `port`, `encryption` from `ConnectionType` → `'ssl'|'tls'|false`, `username`, `password`, `protocol=imap`). News-up `ClientManager` internally so **no DI binding is required** (add `config/Dependencies/ImapBindings.php` only if autowiring `ClientManager` proves necessary).
- `ImapMailbox.php` — thin wrapper over the webklex `Client`/`Folder`: `unreadMessages(): IncomingMailCollection`, `moveToDrafts(IncomingMail)`, `delete(IncomingMail)` (webklex delete = flag + `expunge()`).
- `IncomingMail.php` (our value object: `fromAddress`, `fromName`, `subject`, `toAddresses[]`, `ccAddresses[]`, `textPlain`, `textHtml`, `IncomingAttachments`, + opaque message handle), `IncomingAttachment(s).php` (contentId, filename, contentBytes, contentType, `isInlineCandidate()`), `IncomingMailCollection.php`.
- `CheckMailingLists.php` — inject `MailingListsRepository` + `CheckMailingList`; `__invoke()` → `findAll()->map($checkMailingList)`. Early-return on no lists.
- `CheckMailingList.php` — inject `ImapClientFactory` + `IncomingMailHandler`; per list, connect, fetch unread, map each through the handler. **try/catch per list** so one bad mailbox doesn't abort the whole run.
- `IncomingMailHandler.php` — the core port. Inject `MailerInterface` + `SystemFromAddress`. Build a Symfony `Email` per the **exact rules above** (From with preserved sender name, decorative To, Bcc-minus-sender/To/Cc, internal/external Reply-To, inline-`cid` embed vs attach, text/html bodies). Symfony Mailer **throws** on failure (unlike Craft's boolean) → `try { send } catch { $mailbox->moveToDrafts(); throw; }`; on success `$mailbox->delete()`. Attachment bytes come from webklex in-memory (`getContent()`) — **the old temp-dir `imapAttachmentsPath()` is dropped.** Use case-insensitive email comparison for the dedup/Reply-To checks (small deliberate improvement over Craft's exact match).

### 4. Scheduling (`api/src/MailingLists/Schedule/`)
- `EnqueueCheckMailingLists.php` — mirror `EnqueueGenerateResourcesPagesForRedis`: dedup against `getEnqueuedItems()` by `CheckMailingListsJob::JOB_HANDLE`, else enqueue. (Dedup matters because `ALWAYS` fires every tick.)
- `CheckMailingListsJob.php` — `JOB_HANDLE = 'check-mailing-lists'`, `JOB_NAME = 'Check Mailing Lists'`; inject `Check\CheckMailingLists`; `check()` runs it.
- `CheckMailingListsCommand.php` — register `mailing-lists:check`; `__invoke(): int`.
- `config/ScheduleFactory.php` — add `new ScheduleItem(runEvery: Frequency::ALWAYS, class: EnqueueCheckMailingLists::class, method: 'enqueue')`.
- `config/Events/ApplyCommands.php` — `CheckMailingListsCommand::register(...)`.

### 5. Admin HTTP endpoints (`api/src/MailingLists/Admin/`)
Mirror `Resources/Admin/`, all guarded by the new middleware:
- `GetHasEditMailingListsRoleAction` → `GET /admin/mailing-lists/has-edit-mailing-lists-role`
- `GetMailingListsListAction` → `GET /admin/mailing-lists` (paginated; rows via `asArrayWithoutPassword()`; `PaginatedMailingLists` wrapper)
- `NewMailingList/PostNewMailingListAction` + `NewMailingListFactory` → `POST /admin/mailing-lists/new`
- `EditMailingList/GetEditMailingList/GetEditMailingListAction` → `GET /admin/mailing-lists/edit/{mailingListId}` (incl. subscribers + password for the form; 404 via `RespondWithNotFound`)
- `EditMailingList/PostEditMailingList/PostEditMailingListAction` + `MailingListFactory` → `PATCH /admin/mailing-lists/edit/{mailingListId}`
- `PostDeleteMailingListsAction` → `DELETE /admin/mailing-lists` (`{items: string[]}`)
- `Admin/SubscriberResolver.php` — turns the repeating `subscribers[][name|emailAddress]` body into a `Subscribers` collection, dropping empty rows.
- Register all six in `config/Events/ApplyRoutes.php` under a new `// Mailing Lists` block.

**Auth:** add `case EDIT_MAILING_LISTS;` to `api/src/Auth/UserRole.php`; add `RequireEditMailingListsRoleMiddleware` (extends `RequireRoleMiddleware`, returns the new case — confirmed pattern from `RequireEditResourcesRoleMiddleware`). Roles come from the **OAuth provider's `userinfo.roles`** (string = enum `->name`), so `EDIT_MAILING_LISTS` must be granted to admins **in the external auth provider** — a manual provisioning step, no app DB seed.

### 6. Next.js admin UI (`web/app/admin/mailing-lists/`)
Mirror `web/app/admin/resources/`. Divergence: instead of the file-upload downloads field, a repeating **subscribers** sub-form plus IMAP-settings fields.
- `page.tsx`, `MailingListsPage.tsx` (server) → `MailingListsPageClientSide.tsx` (card list, delete checkboxes, New button; rows show name + address + subscriber count).
- `MailingList.ts` (TS interface; `connectionType: 'ssl'|'tls'|'none'`; `subscribers: {id,name,emailAddress}[]`; password optional/edit-only), `GetMailingLists.ts`.
- `CreateEditMailingListPage.tsx` (shared form: text inputs for list name/address/server/port/username, `type="password"` for password, a select for connection type) + **`SubscribersField.tsx`** (dynamic add/remove rows — model on `ResourceDownloadsField.tsx`).
- `new/*` (POST) and `edit/[mailingListId]/*` (GET/PATCH) pages + submit actions + value/parse types, mirroring the Resources counterparts.
- `HasEditMailingListsRoleGuard/` + `GetHasEditMailingListsRole.ts`.
- Nav: add `'mailingLists'` to the `activeNav` unions in `web/app/admin/Layout/AdminLayout.tsx` and `Sidebar.tsx`; add a sidebar item gated on `userinfo.roles.includes('EDIT_MAILING_LISTS')`.

### 7. composer + DI
- `api/composer.json` — add `"webklex/php-imap": "^6.2"` (pure PHP; **no `ext-imap`, no Dockerfile change**). Implementer runs `composer require webklex/php-imap`.
- DI: prefer **no new binding** (factory news-up `ClientManager`); add `config/Dependencies/ImapBindings.php` only if needed (and wire it where the other `*Bindings` are composed). No new `RuntimeConfigOptions` cases — IMAP creds live per-list in the DB.

---

## Suggested sequencing
1. Migration → migrate up. 2. `composer require webklex/php-imap`. 3. Domain + `ConnectionType`. 4. Persistence + repository. 5. `UserRole` case + middleware. 6. Admin actions + `ApplyRoutes`. 7. IMAP wrappers + handler + check orchestration. 8. Schedule enqueue/job/command + `ScheduleFactory`/`ApplyCommands`. 9. Web UI + nav. 10. Static analysis + manual E2E.

## Verification (this project has **no unit-test suite** — static analysis + manual run)
- **api/**: `composer install`; `vendor/bin/phpcs`; `vendor/bin/phpstan analyse` (watch the webklex boundary types — the wrappers exist so the domain stays fully typed).
- **web/**: eslint + `tsc --noEmit` over the new `mailing-lists` UI.
- **Migration**: run migrate-up against dev DB; confirm both tables, FK, indexes; `migrate:status` shows applied.
- **End-to-end** against a **test IMAP mailbox** (disposable Gmail / Mailpit / GreenMail):
  1. Grant `EDIT_MAILING_LISTS` in the OAuth provider; confirm the nav item + guard.
  2. Create a list pointing at the test mailbox with 2–3 subscribers (include one subscriber address and one non-subscriber to exercise both Reply-To branches).
  3. Send test mail **into** the inbox: as an internal subscriber and as an external non-subscriber; include one with an inline `cid:` image and one with a regular attachment.
  4. Trigger the run — either the schedule command then `queue:consume-next`, or the direct `mailing-lists:check` command for faster iteration.
  5. Verify: correct subscribers got it (sender/To/Cc excluded); From = system address + sender's display name; Reply-To matches internal/external rule; inline image renders, attachment attaches; source message **deleted**.
  6. Failure path: break the mail DSN, re-run → message **moved to Drafts**, error surfaced, other lists unaffected.
  7. Empty inbox → clean no-op; confirm `ALWAYS` dedups (no piled-up queue jobs).

## Key files to read while implementing
- `craft-cms/src/MailingLists/IncomingMailHandler.php` — the exact rules to port.
- `api/src/Resources/ResourcesRepository.php` (+ `Persistence/*`) — repository/persistence pattern.
- `api/src/Contact/SendEmail.php` — Symfony Mailer + `SystemFromAddress`.
- `api/config/Events/ApplyRoutes.php`, `ApplyCommands.php`, `config/ScheduleFactory.php` — the three registration points.
- `web/app/admin/resources/CreateEditResourcePage.tsx` + `ResourceDownloadsField.tsx` — the admin form + repeating-row pattern.
