# Remove Craft CMS and the Nginx Proxy

## Goal

Retire Craft CMS and the Nginx reverse proxy entirely. Next (`web`) becomes the
single ingress for the site host. Traefik routes the site domain straight to
`web`, which now serves everything the proxy used to hand to Craft (favicons,
`/files`, `/uploads`, and the ACME challenge).

Do this for **both dev and prod**.

## Confirmed decisions

- **ACME / TLS:** the `web` container will serve `/.well-known/acme-challenge`
  from the certbot webroot volume. Nginx is removed completely (no minimal
  challenge-only proxy).
- **`/files`:** Next serves it (not nginx).
- **Cross-cutting Traefik middlewares** (www→www redirect, HSTS,
  X-Content-Type, `https-redirect`) are expected to be handled by the external
  prod Traefik stack. `https-redirect` is already defined there (it is
  referenced but not defined in this repo). The www-redirect regex, HSTS, and
  X-Content-Type are currently defined *locally on the proxy* — see the
  verification gate in Ship 4 before assuming they exist globally.
- **Elasticsearch stays** — the API uses it
  (`ELASTIC_SEARCH_HOSTS=http://stmark-elasticsearch:9200`,
  `api/config/Dependencies/ElasticSearch.php`, message search indexing).

## Guardrails for this work

- **Do NOT delete `docker/application/.env` / `.env.local` in Round 1.** The
  `db` service (dev *and* prod) uses `env_file: application/.env` for its MySQL
  config. Uncoupling that is Round 2. Until then, keep the application env files
  and their CI handling intact.
- **Keep these shared volumes:** `uploads-volume` (web + api),
  `files-above-webroot-volume` (web + api), `files-volume` (repurposed to web in
  Ship 2), `web-public-images-galleries-volume`, `db-volume`, `redis-volume`,
  `elasticsearch-data-volume`, `api-storage-volume`.
- **Craft-only volumes safe to drop** (Ship 8): `cp-resources-volume`,
  `image-cache-volume`, `public-cache-volume`, `storage-volume`.
- The Craft `site` database stays for now (harmless); dropping it and its prod
  sync is Round 3.
- The `craft-cms/` directory stays as the on-disk file home for dev mounts and
  `SyncFromProd`. Relocating it is Round 3. Removing containers ≠ deleting that
  directory.

---

## Round 1 — Remove Craft and the Nginx proxy

Ships 1–3 are **additive**: they make `web` capable of serving what the proxy
hands to Craft. Because the proxy still sits in front and still routes those
paths to Craft, these ships cause **no prod behavior change** — they just get
`web` ready. Ships 4+ perform the actual cutover and teardown.

### Ship 0 — Redirect `/cms` → `/admin`

`/cms` was Craft's control-panel path; the new admin lives at `/admin` in the
Next app (`web/app/admin/`, whose root `route.ts` forwards to
`/admin/messages`). Make any old Craft CP bookmark/link land on the new admin
instead of breaking. Implemented twice on purpose: **in nginx now** (while the
proxy still routes `/cms`) and **in Next for later** (so it survives once the
proxy is removed in Ship 4/5 and `/cms` falls through to `web`).

**Behavior:** `/cms` and any path beyond it (`/cms/entries/foo`, etc.) issue a
permanent redirect to exactly `/admin` (path dropped — old Craft CP sub-paths
have no Next equivalent). `/admin` then forwards to `/admin/messages`.

- **nginx now** (`docker/proxy/default.conf.template`): replace the current
  `/cms` Craft proxy block (lines 27–30) with a static redirect:
  ```nginx
  location /cms {
      return 301 /admin;
  }
  ```
  Prefix match covers `/cms` and everything under it; no env substitution
  needed. This block disappears naturally when `docker/proxy/` is deleted in
  Ship 5 — by then the Next redirect has taken over.
- **Next for later** (`web/next.config.js`): add one entry to the existing
  `redirects()` array (same shape as `/sms` etc.):
  ```js
  {
      source: '/cms/:path*',
      destination: '/admin',
      permanent: true,
  },
  ```
  `/cms/:path*` matches `/cms` and `/cms/<anything>`; destination is the flat
  `/admin`. `permanent: true` is Next's 308 (consistent with the other
  redirects here); the nginx side is 301 — both permanent, both target
  `/admin`. While the proxy exists this entry is dormant (nginx intercepts
  first); after the proxy is removed it becomes the live handler.
- **Prod-safe:** isolated to `/cms`, which currently only ever went to Craft.
  No other route is affected.
- **Verify gate:** dev with proxy active (restart proxy so the entrypoint
  re-renders the template) —
  `curl -sI http://stmark.localtest.me/cms` and `…/cms/entries/foo` →
  `301`, `Location: /admin`; confirm `/admin` forwards to `/admin/messages`. For
  the Next side, a local `next build && next start` smoke test of the redirect
  confirms the config entry is valid (it goes live for real after the proxy is
  removed).

### Ship 1 — Move favicon / icon assets into `web/public`

`web/app/layout.tsx` already references `/favicon.ico` and
`https://www.stmarkreformed.com/share.png`; browsers also request
`/apple-touch-icon-*`, `/mstile-*`, and `/favicon-*` by convention. All 20 files
currently live only in `craft-cms/public/`.

- Copy into `web/public/`: `favicon.ico`, `favicon-16x16.png`,
  `favicon-32x32.png`, `favicon-96x96.png`, `favicon-128.png`,
  `favicon-196x196.png`, `apple-touch-icon-57x57.png` … `152x152`,
  `mstile-70x70.png` … `310x310`, `share.png`.
- Confirm they are committed (not gitignored — `web/public/uploads` and
  `web/public/files` are mount points, but the icons should be tracked repo
  assets).
- **Prod-safe:** proxy still serves Craft's copies; nothing changes yet.
- **Verify gate:** none needed in prod; locally confirm the files resolve once
  the proxy is bypassed (deferred to Ship 4 verification).

### Ship 2 — `web` serves `/files`

`files-volume` is currently mounted only into Craft. Repurpose it to `web`.

- **prod compose** (`docker/docker-compose.prod.yml`): add to the `web` service
  volumes: `files-volume:/app/public/files`.
- **dev compose** (`docker/docker-compose.dev.yml`): add to the `web` service
  volumes: `../craft-cms/public/files:/app/public/files`.
- **Prod-safe:** additive mount; proxy still routes `/files` to Craft.
- **Verify gate:** deferred to Ship 4 (after cutover, hit a known `/files/...`
  URL).

### Ship 3 — `web` serves the ACME challenge

The proxy serves `\.well-known\/.+` from `/var/www/letsencrypt` (the host
certbot webroot `/root/certbot/var/www/letsencrypt`). After the proxy is gone,
`web` must answer `/.well-known/acme-challenge/<token>` for the site host or
certbot renewals break.

- **prod compose:** mount the certbot webroot into `web`, e.g.
  `/root/certbot/var/www/letsencrypt:/letsencrypt`.
- **Next:** add a Route Handler at
  `web/app/.well-known/acme-challenge/[token]/route.ts` that reads
  `/letsencrypt/.well-known/acme-challenge/<token>` and returns it as
  `text/plain`. (A Route Handler is used rather than `public/.well-known`
  because Next does not reliably serve dot-directories from `public/`.)
- **Prod-safe:** additive; proxy still answers the challenge while it exists.
- **Verify gate (important):** before Ship 4, confirm with a manual file drop
  that `web` serves
  `http://<web>/.well-known/acme-challenge/<test-token>` correctly. This is the
  riskiest piece — a broken challenge means cert renewal failure ~weeks later.

### Ship 4 — Cutover: move Traefik routing from proxy to `web`

Do the label move and proxy-service removal in **one commit per environment**
so there is never a window with two routers claiming the same host.

- **prod compose `web` service** — add a `deploy.labels:` block mirroring the
  proxy's site routing:
  - `traefik.enable=true`
  - `traefik.http.services.stmark_prod.loadbalancer.server.port=80`
  - `traefik.docker.lbswarm=true`
  - `traefik.http.routers.stmark_prod.entrypoints=web`
  - `traefik.http.routers.stmark_prod.rule=Host(\`www.stmarkreformed.com\`)`
  - `traefik.http.routers.stmark_prod.middlewares=https-redirect`
  - `traefik.http.routers.stmark_prod_secure.entrypoints=websecure`
  - `traefik.http.routers.stmark_prod_secure.tls=true`
  - `traefik.http.routers.stmark_prod_secure.rule=Host(\`www.stmarkreformed.com\`)`
  - **Verification gate:** confirm whether the www→www redirect, HSTS, and
    X-Content-Type middlewares already exist in the external Traefik stack. If
    **not**, port the proxy's `redirectregex` / `headers` middleware labels onto
    `web` here too. Do not drop them on faith.
- **dev compose `web` service** — add the `stmark_local` / `stmark_local-secure`
  router labels currently on the proxy (Host `stmark.localtest.me`,
  `traefik.docker.network=traefik-dev_default`, https-redirect, tls). Remove the
  `internal_web_container` alias only if nothing else references it (proxy is
  the only referencer — safe to drop with the proxy).
- **Remove the `proxy` service** from both compose files, plus the
  `depends_on: - proxy` / `- web` / `- app` entries that referenced it.
- **Prod-safe cutover:** single deploy flips ingress to `web`.
- **Verify gate (full smoke test):** site loads over https; favicon and
  `share.png` resolve (Ship 1); a `/files/...` URL resolves (Ship 2); an
  `/uploads/...` URL resolves (already mounted); the ACME route responds
  (Ship 3); HMR/websocket still works in dev.

### Ship 5 — Remove proxy build + CLI plumbing

- **CI** (`.github/workflows/ci.yml`): delete the `build-proxy` job; remove
  `build-proxy` from `deploy.needs`; remove the
  `docker pull …-proxy` line; remove `docker/proxy/.env` from the `scp`
  `source:` list; remove the `cat …/proxy/.env.local >> …/proxy/.env` line.
- **dev CLI** (`devCliSrc/Shared/DockerImage.php`): remove the `proxy` case.
- **`devCliSrc/Shared/EnsureDevEnvFiles.php`**: remove the
  `docker/proxy/.env.local` touch.
- Delete `docker/_ephemeral-storage/dockerfile-hashes/proxy`.
- Delete the `docker/proxy/` directory.
- **Verify gate:** `dev docker:build` and `dev docker:up` run clean; CI deploy
  succeeds.

### Ship 6 — Remove Craft app runtime services

After Ship 4 these are already unreachable. Remove from **both** compose files:

- `app`
- `app-scheduled-task-runner`
- `app-queue-consumer-1`
- In dev, the `internal_craft_container` alias and the `app` port mapping
  (`44232:80`) go with the `app` service.
- **Prod-safe:** services are orphaned/unreachable after cutover.
- **Verify gate:** stack deploys with the services gone; nothing else
  references `stmark-app` (already confirmed: `web`'s `APP_API_URL` is dead —
  see Ship 9).

### Ship 7 — Remove Craft app build + CLI plumbing

- **CI**: delete the `build-app` and `build-app-dependent-schedule-runner`
  jobs; remove both from `deploy.needs`; remove the `docker pull …-app` and
  `…-app-schedule-runner` lines.
  - **Keep** `docker/application/.env` in the `scp` `source:` list and its
    `cat …/application/.env.local >> …/application/.env` line — `db` still needs
    it until Round 2.
- **dev CLI** (`DockerImage.php`): remove the `app` and `appScheduleRunner`
  cases and their arms in `getDashCaseName()` and `dockerfilePath()`.
- Remove `ContainerAppCommand` registration in
  `devCliSrc/Events/ApplyCliCommandsEventSubscriber.php` and delete
  `devCliSrc/Commands/Docker/Container/ContainerAppCommand.php`.
- **`EnsureDevEnvFiles.php`**: remove the `docker/application/.env.local` touch
  **only if** `db`'s env handling no longer needs it created locally — since db
  still reads `application/.env.local`, **keep this touch** until Round 2.
- Delete `docker/_ephemeral-storage/dockerfile-hashes/app` and
  `…/appScheduleRunner`.
- Delete Craft image build context: `docker/application/` **except** `.env` and
  `.env.local` (keep those for `db`), and delete `docker/schedule-runner/`.
- **Verify gate:** `dev docker:build`/`up` clean; CI deploy succeeds; db still
  comes up with its MySQL config.

### Ship 8 — Remove Craft-only volumes and the `utility` service

- **prod compose:** remove volumes `cp-resources-volume`, `image-cache-volume`,
  `public-cache-volume`, `storage-volume` (definitions + any remaining Craft
  mounts). **Do not** remove `files-volume` (now web), `uploads-volume`,
  `files-above-webroot-volume`, `web-public-images-galleries-volume`,
  `api-storage-volume`, `db-volume`, `redis-volume`,
  `elasticsearch-data-volume`.
- **`utility` service** (prod compose) — Craft maintenance container; mounts the
  Craft volumes. `migrate.sh` uses the api/auth images directly, not utility.
  **Verify** nothing else invokes it, then remove the `utility` service, the
  `build-utility` CI job (+ `deploy.needs` + `docker pull` line), and the
  `utility` case from `DockerImage.php` and its hash file.
- **Verify gate:** stack deploys; api search still works (Elasticsearch
  untouched).

### Ship 9 — Dead-config cleanup (optional, low-risk)

- `web/.env` / `web/.env.local`: remove `APP_API_URL` (and `APP_URL`-style
  Craft entries) — verified unused.
- `web/app/ServerSideRunTimeConfig.ts`: remove the `APP_API_URL` member from the
  `ConfigOptions` enum (never consumed).
- **Verify gate:** `tsc`/`eslint` clean.

---

## Round 2 — Uncouple the `db` env from `application/.env`

The `db` service reads `env_file: application/.env` purely for `MYSQL_*` /
root-password config — an incidental Craft-era coupling.

- Create `docker/db/.env` (+ `.env.local`) with the MySQL vars.
- Point the `db` service `env_file` at `docker/db/.env` (dev + prod).
- CI: add `docker/db/.env` to the `scp` `source:` and add a
  `cat …/db/.env.local >> …/db/.env` line; **remove** the `application/.env`
  entries from `scp` source and the cat line.
- `EnsureDevEnvFiles.php`: touch `docker/db/.env.local`; remove the
  `docker/application/.env.local` touch.
- Delete `docker/application/` entirely (the last `.env` files go here).
- **Verify gate:** db comes up with correct credentials in dev and prod.

---

## Round 3 — Relocate Craft file storage and delete `craft-cms/`

Dev mounts and `SyncFromProd` still point at `craft-cms/`:
- `web` + `api` dev mounts: `../craft-cms/public/uploads`,
  `../craft-cms/filesAboveWebroot`, and (added in Ship 2)
  `../craft-cms/public/files`.
- `SyncFromProd/FilesSync.php` rsyncs into `craft-cms/public/files`,
  `craft-cms/filesAboveWebroot`, `craft-cms/public/uploads`.

Steps:

- Choose a neutral on-disk home (e.g. `shared-files/` at repo root) for
  `uploads`, `files`, `filesAboveWebroot`.
- Update dev `web` + `api` mounts in `docker-compose.dev.yml` to the new paths.
- Update `SyncFromProd/FilesSync.php` `FilesDirectoryMapping` targets to the new
  paths.
- Migrate existing on-disk data into the new location.
- Drop the `stmarkreformed.sql → site` mapping in
  `SyncFromProd/DatabaseSync.php`, and consider dropping the `site` database
  from local and prod.
- Delete the `craft-cms/` directory (Craft code + assets) once nothing
  references it.
- **Verify gate:** `dev sync-from-prod` (or equivalent) lands files in the new
  location; web serves uploads/files from there.

---

## Open verification items (carry into the ships above)

1. **Global Traefik middlewares** — confirm www-redirect / HSTS /
   X-Content-Type exist in the external Traefik stack; if not, port them to
   `web` labels in Ship 4.
2. **ACME route** — manually confirm `web` serves a dropped challenge token
   before removing the proxy (Ship 3 gate).
3. **`utility` service** — confirm no out-of-band ops use it before removal
   (Ship 8).
4. **Dev HMR over Traefik** — confirm Next HMR websocket works routing directly
   to `web` without the proxy's `/_next/webpack-hmr` location (Ship 4 gate).
