# Resumable Chunked File Uploads — IMPLEMENTED

Status: built across API + web + docker. Verified with phpstan (level max),
phpcs, and tsc. eslint could not be run locally (broken ajv dep in this env) and
must be run in CI. Not yet manually tested over a throttled connection — see
Verification.

## Why

Admins could not reliably submit a sermon in prod: the upload 502'd on a
slow/flaky connection (`[Error: aborted] { code: 'ECONNRESET' }`) but succeeded
when fast. Root cause: the form posted the raw file to a Next.js **server action**,
which buffers the whole body before any code runs; on a flaky link the half-read
request aborts and Traefik returns 502. Fix: resumable chunked upload — a dropped
connection only loses the in-flight chunk, which retries/resumes.

Generalized across all four file-upload features, not messages-only.

## What was built

### Generic API module — `api/src/Uploads/`
Feature-agnostic, autowired (no DI entries needed). Endpoints under `/admin/uploads`
gated by `App\Auth\RequireAnyEditRoleMiddleware` (passes for any content-edit role;
real authorization stays on each feature's own create/edit route):
- `POST /admin/uploads` initiate, `PUT /admin/uploads/{id}/chunk/{index}` (idempotent),
  `GET /admin/uploads/{id}` status (for resume), `POST /admin/uploads/{id}/complete`
  (assemble + size check + accept-type validation).
- Classes: `UploadSession` (manifest VO), `UploadAcceptType` enum (mp3|pdf|any),
  `ReceivedChunkIndices` (typed collection), `UploadChunkStore` (sole owner of
  `/var/www/storage/uploads`, UUID-validates ids against path traversal),
  `UploadAssembler` (stream-append, never whole-file in memory),
  `UploadContentTypeValidator` (header-bytes only), `UploadRepository`,
  `StagedUploadLocator` (read seam), `UploadClaim` (cross-volume copy+unlink into a
  feature's final path), `UploadHandleSize` (resolves byte size from the staging
  session before claim). Actions + `UploadProgressResponse` in `Uploads/Http/`.
- Handle format: `upload:{uploadId}` (`UploadHandle`). Replaces the old `data:`
  base64 detection; an unchanged stored path never carries the prefix, so the edit
  "file unchanged" case still works.
- GC: `Uploads/Purge/PurgeAbandonedUploads` + `EnqueuePurgeAbandonedUploads`,
  registered FIVE_MINUTES in `config/ScheduleFactory.php`; deletes staging dirs
  older than 24h.

### API feature integration (handle replaces base64)
Each feature detects `UploadHandle::isHandle()` and calls `UploadClaim::claimTo()`
with its existing filename/dir scheme; size recorded via `UploadHandleSize` before
the claim. Touched:
- Messages: `AudioValidationEntityTrait`, `PersistMessageAudioFile` (→ UploadClaim),
  `MessageAudioFileStorage` (stripped to `delete()`), `NewMessageFactory`,
  edit `MessageFactory`, `UpdatedMessageFactory` (override audio + size only when a
  new upload). Internal-messages mirror these exactly.
- Hymns: `HymnUploadResolver` + `HymnFileStorage` (sheet ext from session fileName;
  tracks `{base}.mp3`). Resources: `ResourceUploadResolver` + `ResourceFileStorage`.

### Web — `web/app/admin/Forms/FileUploads/`
- `chunkedUpload.ts` engine: `uploadFileResumably()` — `File.slice` at 2 MB,
  sequential PUTs with exponential-backoff retry, resume via status, uploadId
  persisted in `sessionStorage` (namespaced by field+row+name+size).
- `ChunkedFileUploader.tsx` (replaced `SingleFileUploader`, deleted) — single file,
  progress, writes `upload:{id}` to the hidden input, `onUploadingChange` callback.
- `ChunkedMultiFileField.tsx` — config-driven multi-row (discriminated `metaField`:
  editable title vs auto filename); `HymnPracticeTracksField` and
  `ResourceDownloadsField` are now thin wrappers over it.
- Route-handler proxies `web/app/api/admin/uploads/...` (NOT server actions): read
  the raw chunk, base64 it for the JSON-only `web→api` hop, forward via
  `makeWithToken` (carries the user's OAuth token for free, like keep-alive).
- Forms (messages, internal-messages, hymns, resources) swap to the chunked
  components and disable submit while any upload is in flight. Server actions drop
  `FileToBase64` (both deleted) — `audioPath` now carries the handle or stored path.

### docker
- `docker/api/nginx/nginx.conf`: `client_body_timeout` and `send_timeout` 15 → 60
  (belt-and-suspenders; chunking makes them non-binding).

## Key decisions / invariants
- Never hold a whole file in memory on the API (stream assemble + stream copy).
- Cross-volume claim is copy+unlink (storage vs uploads are separate mounts); a
  DB-write failure after claim can orphan a final file (harmless, overwritten) —
  GC only sweeps staging dirs.
- Size flows from the staging session's `totalSize` into the row before the claim
  discards the session; on edit, size is only overridden when a new upload occurs,
  preserving the stored size on audio-unchanged edits.

## Verification
- Done: phpstan (max), phpcs, tsc all clean.
- TODO (manual, per project's no-test-suite convention): for each of the four
  features, upload over DevTools "Slow 3G" with a forced mid-upload offline toggle;
  confirm chunk retry + resume-from-status, a clean pre-submit error on a wrong file
  type, successful create + edit, correct final path + playable/openable file, and
  EDIT-with-no-new-file leaves the stored file untouched. Run eslint in CI. Confirm
  the framework parses JSON bodies for PUT (chunk endpoint reads `parsedBody.data`).
