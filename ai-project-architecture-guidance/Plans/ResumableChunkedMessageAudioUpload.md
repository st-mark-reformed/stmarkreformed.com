# Resumable Chunked Message Audio Upload

## Goal

Make uploading a sermon MP3 survive a flaky/slow client connection. Today a single
upload that drops mid-flight fails the whole submit. Replace the
"buffer-the-whole-file-then-base64-it" flow with a **resumable chunked upload**:
the file is sliced into small chunks, each chunk is uploaded independently and
retried on failure, and a dropped connection only loses the in-flight chunk
(which retries / resumes) rather than the whole file.

This also removes the current in-memory base64 inflation path, which is the most
memory- and timeout-sensitive way to move the file.

## Why this is needed (root cause)

Confirmed by behavior + code, not the original timeout guess:

- The form posts the raw MP3 to a Next.js **server action**. Server actions
  **buffer the entire request body** before any code runs — nothing happens until
  all ~15 MB has arrived.
- The web container is **Next running directly on port 80** behind Traefik
  (`docker/web/Dockerfile:49-64`), so it is `Traefik → Node` with no nginx buffer
  in front.
- On a flaky link the upload stalls/drops, Node aborts the half-read request
  (`[Error: aborted] { code: 'ECONNRESET' }`), Traefik returns **502**.
- The base64 conversion (`web/app/admin/messages/FileToBase64.ts`) and the
  `web → api` POST only happen *after* the full file arrives, so they are not
  implicated. The failure is on the **browser → web** leg.
- It is connection-speed dependent (fails slow, succeeds fast; same 15 MB file),
  which is exactly the signature of an interrupted single upload.

Timeout tuning alone does not fix this — it only helps "slow but stable," not
"dropped." Resumability is the durable fix.

## Current flow (what we are replacing)

Web:
- `web/app/admin/messages/CreateEditMessagePage.tsx` renders
  `web/app/admin/Forms/FileUploads/SingleFileUploader.tsx`, which uses
  `FileReader.readAsDataURL` to produce `data:audio/mpeg;base64,...` into a hidden
  input `audioPath`, plus a hidden `audioPathFile` carrying the raw `File`.
- `web/app/admin/messages/new/CreateNewMessageSubmitFormAction.ts` (and the edit
  equivalent `web/app/admin/messages/edit/[messageId]/EditMessageSubmitFormAction.ts`)
  read the file, base64 it, and put it on `payload.audioPath`, then
  `RequestFactory().makeWithToken({ uri: '/admin/messages/new', method: POST, payload })`.
- `web/app/api/request/RequestFactory.ts` (`rxante-oauth`) sends **JSON only**,
  with the logged-in user's OAuth token, to `API_URL` (`http://stmark-api`,
  internal network — not Traefik).

API:
- Route registered in `api/config/Events/ApplyRoutes.php` via
  `PostNewMessageAction::applyRoute()`; action at
  `api/src/Messages/Admin/NewMessage/PostNewMessageAction.php`, gated by
  `RequireEditMessagesRoleMiddleware`.
- `NewMessageFactory::createFromRequest()` reads `audioPath` as a base64 string.
- `MessageAudioFileStorage::save()` strips the data URI, `base64_decode`s,
  validates the MP3 signature, writes to `/var/www/public/uploads/audio/{slug}.mp3`.
- MP3 validation + filename logic lives in
  `api/src/Messages/AudioValidationEntityTrait.php`; persistence in
  `api/src/Messages/Persistence/Persist/PersistMessageAudioFile.php` and
  `.../Create/CreateMessage.php` (transaction wraps DB insert + file write).

Volumes (`docker/docker-compose.prod.yml`):
- `api-storage-volume:/var/www/storage` — private, shared across `api`,
  `api-queue-consumer`, `api-schedule-runner`. **Good home for in-progress chunks.**
- `uploads-volume:/var/www/public/uploads` — final web-served audio.

## Design decisions (locked in)

1. **The API owns chunk storage, assembly, and validation.** The API already owns
   the uploads + storage volumes and the MP3 validation. Keep all file behavior
   there (locality). The web tier is a thin authenticated proxy for chunk bytes.

2. **Chunks travel browser → Next Route Handler → API.** The browser cannot call
   the API directly (the OAuth token is held server-side, per session). It also
   must **not** use a server action (those buffer the whole body — the exact thing
   we're removing). So chunk traffic goes to **Next.js Route Handlers**
   (`web/app/api/admin/messages/audio-upload/.../route.ts`), which read a small
   chunk body and forward it to the API with `makeWithToken`.

3. **Raw binary over the flaky leg; base64 only over the reliable internal leg.**
   The browser → web hop carries **raw chunk bytes** (no inflation — minimizes
   bytes over the bad link). Because `makeWithToken` is JSON-only, the route
   handler base64-encodes the *small* chunk for the internal web → api hop. Per-
   chunk base64 of a ~2 MB chunk is cheap and the internal network is reliable.
   This avoids touching the shared `rxante-oauth` wrapper.
   - Alternative (rejected for now): extend the request layer to send raw bodies.
     More invasive, touches shared auth/refresh/locking logic. Revisit only if the
     internal base64 cost ever matters.

4. **In-progress chunks live on `api-storage-volume`**, not the web-served uploads
   volume: `/var/www/storage/messageAudioUploads/{uploadId}/`. Final assembled
   file still lands at `/var/www/public/uploads/audio/{slug}.mp3`.

5. **The message submit references an upload handle, not the file.** After all
   chunks finish + the file validates, the form submits `audioUploadId` instead of
   base64 `audioPath`. Create/edit move the assembled staged file into place.

6. **Chunk size:** default 2 MB (tunable constant shared client/server). Sequential
   upload (concurrency 1) keeps it simple and is fine for a single 15 MB file;
   resumability, not throughput, is the win.

7. **No new test suite** — project has none. Verify with phpcs / phpstan / eslint /
   tsc + manual upload over a throttled connection.

## Upload protocol (resumable)

All endpoints API-side, under the Messages feature, gated by
`RequireEditMessagesRoleMiddleware`. Reached through Next route-handler proxies.

1. **Initiate** — `POST /admin/messages/audio-upload`
   Body: `{ fileName, totalSize, chunkSize, totalChunks }`.
   Creates `/var/www/storage/messageAudioUploads/{uploadId}/` + a `manifest.json`
   (fileName, totalSize, chunkSize, totalChunks, createdAt).
   Returns `{ uploadId, receivedChunks: [] }`.

2. **Put chunk** — `PUT /admin/messages/audio-upload/{uploadId}/chunk/{index}`
   Body: the chunk (base64 over internal hop). Writes `{index}.part`. **Idempotent**
   — re-PUTting an index overwrites. Returns `{ received: index }`.

3. **Status (for resume)** — `GET /admin/messages/audio-upload/{uploadId}`
   Returns `{ receivedChunks: number[], totalChunks }`. Client calls this on retry
   / page reload to skip already-stored chunks.

4. **Complete** — `POST /admin/messages/audio-upload/{uploadId}/complete`
   Verifies all indices present, assembles them in order into `assembled.mp3`,
   checks assembled size == `totalSize`, validates MP3 signature. Returns
   `{ uploadId, valid: true }` or a validation error. The `uploadId` is now the
   handle passed to message create/edit.

Client behavior: persist `uploadId` keyed by `fileName + size` (component state +
`sessionStorage`) so a reload/drop can resume. Each chunk PUT retried with
exponential backoff; on resume, call status first and upload only missing indices.

## API work

Locality: new code under `api/src/Messages/AudioUpload/` (co-located with the rest
of the Messages feature). Follow existing patterns: static `applyRoute()`, ctor
injection, `Result` returns, typed collections, enums, `strict_types=1`.

New collaborators (small, role-based):

- `AudioUploadSession` — value object: uploadId, fileName, totalSize, chunkSize,
  totalChunks. Constructed from / serialized to the manifest.
- `AudioUploadStorage` — reads/writes chunk `.part` files + `manifest.json` under
  `/var/www/storage/messageAudioUploads/{uploadId}/`. Knows the base dir; injected.
- `ReceivedChunks` — typed collection wrapping the set of received chunk indices
  (no raw arrays across boundaries, per `AGENTS.md`).
- `AssembleAudioUpload` — concatenates parts in order, checks size, validates MP3,
  produces the staged `assembled.mp3`. Returns `Result`.
- `AudioUploadRepository` — orchestrates initiate / put / status / complete over
  `AudioUploadStorage`.
- Four action classes mirroring `PostNewMessageAction` shape:
  `PostInitiateAudioUploadAction`, `PutAudioUploadChunkAction`,
  `GetAudioUploadStatusAction`, `PostCompleteAudioUploadAction` — each with
  `applyRoute()` + `RequireEditMessagesRoleMiddleware`, registered in
  `api/config/Events/ApplyRoutes.php` next to the other message routes.

Reuse / small refactor:

- Extract the MP3 signature check currently embedded in
  `AudioValidationEntityTrait` / `MessageAudioFileStorage::isMp3()` into a tiny
  `Mp3SignatureValidator` so both the assembler and the existing storage share one
  implementation. (Two callers now genuinely need it — justified extraction, not
  speculative.)

Wire the handle into create/edit:

- `NewMessageFactory` / edit factory: accept `audioUploadId` (string, optional)
  alongside the existing `audioPath` (used for the "audio unchanged on edit" case).
- `PersistMessageAudioFile::persist()`: when an `audioUploadId` is present, **move**
  the staged `assembled.mp3` into `/var/www/public/uploads/audio/{slug}.mp3`
  (rename within the same... note: storage volume and uploads volume are separate
  mounts, so this is a copy+unlink, not an atomic rename — handle accordingly),
  record `audioFileSize` from the assembled file, then delete the upload temp dir.
  Keep the existing base64 path temporarily for safety, or remove once the new
  flow is verified (see Rollout).
- Keep the create/edit transaction semantics intact; the file move happens in the
  same persist step as today.

Cleanup (abandoned uploads):

- Add a scheduled GC that deletes `messageAudioUploads/{uploadId}/` dirs whose
  `manifest.createdAt` is older than N hours (e.g. 24). Follow the
  `api-schedule-runner` / `ScheduleFactory` pattern already used by
  `IndexAllMessages`.

## Web work

Locality: under `web/app/admin/messages/` (feature-local), behavior co-located in
the component per the React rules.

- New uploader component (e.g. `ChunkedAudioUploader.tsx`) replacing
  `SingleFileUploader` **for messages only** (leave other features on the existing
  uploader). Reuse the existing drag-drop + `.mp3` validation UX. It:
  - validates extension, slices via `File.slice` into `chunkSize` chunks,
  - calls initiate, uploads chunks sequentially with retry/backoff + progress UI,
  - resumes via the status endpoint using a persisted `uploadId`,
  - on complete, writes `audioUploadId` into a hidden input; surfaces errors inline.
- New Next route handlers (NOT server actions) under
  `web/app/api/admin/messages/audio-upload/...`:
  - `POST .../audio-upload/route.ts` (initiate)
  - `PUT  .../audio-upload/[uploadId]/chunk/[index]/route.ts`
  - `GET  .../audio-upload/[uploadId]/route.ts` (status)
  - `POST .../audio-upload/[uploadId]/complete/route.ts`
  Each reads the (small) request, resolves the user's token the same way existing
  admin server actions do, and forwards to the API via `makeWithToken` (base64 the
  chunk body). They must enforce the same auth as the existing admin actions before
  proxying.
- `CreateNewMessageSubmitFormAction.ts` / `EditMessageSubmitFormAction.ts`: drop
  `FileToBase64` + base64 `audioPath`; pass `audioUploadId` (new file) or the
  existing `audioPath` (unchanged-on-edit). Update
  `CreateEditMessageParseFormData` / `CreateEditMessageSubmitActionState` types.
- `FileToBase64.ts` becomes dead for messages — remove if no other caller.

## Edge cases & details

- **Cross-volume move:** `/var/www/storage` and `/var/www/public/uploads` are
  different mounts → `rename()` across them fails. Use copy + unlink (or stream).
- **Validation timing:** validate MP3 signature + total size at **complete**, before
  the message submit, so the user gets a clean error pre-submit rather than a failed
  create.
- **Idempotent chunk PUTs** make retries safe; **status before resume** avoids
  re-uploading stored chunks.
- **Abandoned uploads** are GC'd on a schedule; complete also cleans up its own temp
  dir after a successful move.
- **Size limits:** chunks are tiny, so the 100 MB Next server-action body limit and
  nginx `client_max_body_size` are no longer the binding constraint. The nginx
  body/`send` timeouts (`docker/api/nginx/nginx.conf:27-30`, currently 15s) stop
  being a factor for the big payload, though bumping them remains harmless hardening.
- **Auth:** route handlers must not become an unauthenticated proxy — gate them with
  the same session check the existing admin server actions rely on; the API still
  enforces `EDIT_MESSAGES`.

## Rollout

1. Build the API endpoints + storage/assembly/GC; verify with `curl` + phpstan/phpcs.
2. Build the web route handlers + uploader; wire create/edit to `audioUploadId`.
3. Keep the old base64 `audioPath` path working until the new flow is verified in
   prod over a throttled connection, then remove `FileToBase64` + the base64 branch.
4. Manual verification: upload a real sermon over a deliberately throttled/flaky
   connection (DevTools throttling); confirm chunk retries/resume and a successful
   create + playable file. Run eslint/tsc + phpcs/phpstan to zero warnings.

## Open questions

- Confirm exactly how existing admin server actions resolve/guard the user session,
  so the new route handlers reuse the same guard (not a weaker one).
- Confirm `audioPath` stored on the message is just `{slug}.mp3` (the migration plan
  assumes filename-only) so the assembled-file move targets the right name.
- Decide whether to also bump `docker/api/nginx/nginx.conf` body/send timeouts as
  belt-and-suspenders, or leave them (chunking makes them non-binding).

## Non-goals

- A full tus.io server implementation (heavier than needed; this custom protocol
  covers resume + retry for our single-file case).
- Changing uploads for other features (men-of-the-mark, resources, etc.) — they keep
  the existing `SingleFileUploader`.
- Parallel/multi-connection chunk upload (sequential is sufficient at 15 MB).
