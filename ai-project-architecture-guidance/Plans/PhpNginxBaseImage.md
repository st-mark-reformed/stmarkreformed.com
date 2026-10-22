# Move api and auth to rxantesystems/php-nginx (PHP 8.5.12)

## Goal

The api and auth images run PHP 8.4.5, which has a major security
vulnerability. Move both to `rxantesystems/php-nginx:8.5.12-fpm-trixie-r1`,
which ships the patched PHP, and delete everything in our Dockerfiles and
`docker/` config that the base image already provides. licenses.tjdraper.com
(tjdraper.com-v8) is already on this image and is the reference.

While we're here:

- Fix `migrate.sh`, which breaks on the base image's s6-overlay v3 (see
  `~/Desktop/smrc-migrate-entrypoint-bug.md`).
- Make failed migrations fail the deploy (bootstrap 1.4.0).
- Move the utility image off `ubuntu:18.04`.

## What the base image provides

Source: `~/Downloads/php-nginx`. It covers everything our api and auth
Dockerfiles build themselves:

- the `/bin` workaround is gone, s6-overlay v3.2.3.2, `/init` entrypoint,
  `STOPSIGNAL SIGTERM`
- apt utilities (curl, gnupg, zip, unzip, vim, openssl, gettext-base, …)
- PHP extensions: a superset of ours (gd with jpeg/freetype/webp/xpm,
  mbstring, zip, pcntl, pdo_mysql, bcmath, redis; opcache is built into 8.5)
- nginx 1.31.7 mainline with the same module set
- composer 2.10.3, `COMPOSER_ALLOW_SUPERUSER=1`
- `php.ini-production` as `php.ini`, plus its own `conf.d/php.ini`,
  `opcache.ini`, php-fpm pool template, `nginx.conf`, `default.template`
- xdebug in `$PHP_INI_DIR/disabled`, enabled by `ENABLE_XDEBUG=1`
- `ENABLE_PHP_DEV_CONFIG=1` dev config (also sets `opcache.revalidate_freq=0`)
- `/root/.profile` and `/root/.bashrc`
- Datadog tracer, switched off by `ENABLE_DATADOG=0` (see below)

## Decisions

- **Image tag:** `8.5.12-fpm-trixie-r1` (amd64 and arm64 on Docker Hub).
- **Take the image's defaults** for every setting we used to override (nginx
  timeouts and keepalive, fpm `pm.max_requests`, `display_startup_errors`,
  `upload_max_filesize`, xdebug modes).
- **One exception: `mysqlnd.net_read_timeout = 60` for the api.** It bounds
  MySQL reads alongside the PDO connect timeout and Redis timeouts from
  ef5f79c. Without it the schedule runner has no bound on a stuck query, and
  cron starts a new run every minute. It moves out of ini and into
  `ApiPdoFactory`, as `ini_set()` next to `PDO::ATTR_TIMEOUT` (the setting is
  `PHP_INI_ALL`). So `docker/api/php/` goes away entirely.
- **Datadog off as Dockerfile `ENV`**, not in `.env` files: the migrations,
  queue consumer, and schedule runner never run the image's init (which is
  what applies `ENABLE_DATADOG`), so the `DD_*` switches go in too. The
  queue-consumer and schedule-runner images inherit them from the api image,
  and cron gets them through `printenv`.
- **Certbot:** api and auth both renew their certificates through their own
  nginx webroot rule. Each ships its own `default.template`: the image's
  template plus the ACME location, narrowed from `/.well-known/.+` to
  `/.well-known/acme-challenge/` as licenses does. Auth's
  `/well-known/openid-configuration` has no leading dot and is unaffected.
  Cost: if the image's template changes, we carry the change over by hand.
- **composer `"php"` to `^8.5`** in api and auth. `devCliSrc` stays `^8.4`,
  since it runs on the host.
- **`rxante/php-app-bootstrap` to `^1.4`** in api and auth, with
  `runApplication(exitWithStatusCode: true)` in each `cli`.
- **Utility image:** `ubuntu:26.04`.

## Changes

### docker/api/Dockerfile

Becomes:

```dockerfile
FROM rxantesystems/php-nginx:8.5.12-fpm-trixie-r1

# No Datadog agent runs on this host. The DD_* switches cover the processes
# that skip the image's init (migrations, queue consumer, schedule runner),
# since init is what applies ENABLE_DATADOG.
ENV ENABLE_DATADOG=0 \
    DD_TRACE_ENABLED=0 \
    DD_INSTRUMENTATION_TELEMETRY_ENABLED=0 \
    DD_TRACE_SIDECAR_TRACE_SENDER=0

# The image's server block plus the certbot webroot
COPY docker/api/nginx/default.template /etc/nginx/conf.d/default.template

COPY api /var/www

RUN composer install --no-interaction --no-ansi --no-progress --optimize-autoloader --no-dev

HEALTHCHECK --interval=5s --timeout=5s --start-period=10s --retries=10 CMD curl --fail http://localhost/healthcheck/659f105793f58 || exit 1
```

### docker/auth/Dockerfile

The same shape, keeping the `frontend-builder` stage and its
`COPY --from=frontend-builder` of `public/assets`.

### docker/api/nginx/default.template, docker/auth/nginx/default.template (new)

A copy of the image's `config/nginx/default.template` with this added before
`root /var/www/public;`:

```nginx
    # certbot's HTTP-01 challenges, from the webroot mounted in
    # docker-compose.prod.yml
    location ^~ /.well-known/acme-challenge/ {
        root /var/www/letsencrypt;
        try_files $uri =404;
    }
```

Leave the `${NGINX_ACCESS_LOG}` / `${NGINX_ERROR_LOG}` placeholders, which the
image's nginx `run` script fills in with `envsubst`.

### Deleted

In both `docker/api/` and `docker/auth/`:

- `cont-init.d/00-php-config`
- `services.d/nginx/finish`, `services.d/nginx/run`, `services.d/php-fpm/run`
  (the `finish` script uses s6 v1 paths and would break under v3)
- `php/php-fpm.conf`, `php/php.ini`, `php/xdebug.ini`
- `nginx/nginx.conf`, `nginx/default.conf`

`docker/.profile` stays, because the web image still copies it.

### api/src/Persistence/ApiPdoFactory.php

`ini_set('mysqlnd.net_read_timeout', '60')` before the `PDO` is constructed,
with a comment explaining the bound. Check that `ini_set` doesn't return
`false`.

### api/composer.json, auth/composer.json, cli scripts

- `"php": "^8.5"`
- `rxante/php-app-bootstrap` to `^1.4`
- `->runApplication(exitWithStatusCode: true)` in `api/cli` and `auth/cli`
- Refresh both `composer.lock` files inside the new image

### devCliSrc/PhpVersionHandler.php

Update the `brew` hint from `php@8.4` to `php@8.5`.

### .github/workflows/migrate.sh

Make `php` the entrypoint so the migration skips `/init`, which in s6 v3 runs
the command without the container's environment:

```bash
    --entrypoint php
...
docker_args+=(
    "$IMAGE"
    cli migrate:up
)
```

Drop `COMMAND` and `bash -c`, and replace the `--entrypoint ""` line with a
comment saying why `php` is the entrypoint: `docker service create` treats an
empty entrypoint as unset. Nothing in `cont-init.d` is needed by migrations:
our only script picks the dev or prod PHP config.

### docker/api-queue-consumer, docker/api-schedule-runner

No changes expected. Both keep `ENTRYPOINT []` and their own `CMD`. Confirm
that the schedule runner's `cron` install works on trixie.

### docker/utility/Dockerfile

`FROM ubuntu:26.04`.

## Verification

- Build api, auth, queue-consumer, schedule-runner, and utility images
  locally with the dev CLI.
- In the api and auth containers:
  - `php -v` reports 8.5.12
  - `php -m` lists the extensions the apps need
  - `nginx -t` passes
  - the healthcheck URL returns 200
- Put a file in `/var/www/letsencrypt/.well-known/acme-challenge/` and fetch
  it through nginx.
- In the api, confirm `mysqlnd.net_read_timeout` is 60 at runtime through the
  PDO factory.
- `ENABLE_XDEBUG=1` still enables xdebug in the dev api container.
- The dev stack comes up: web, api, and auth pages load; the queue consumer
  stays healthy; the schedule runner logs a run.
- Run migrations the way `migrate.sh` will:
  `docker run --rm --entrypoint php --env-file … <image> cli migrate:up`
  works. A deliberately failing command exits non-zero now that bootstrap
  1.4 passes the exit code through.
- phpcs and phpstan pass with zero warnings in api and auth on PHP 8.5.

`migrate.sh` itself only runs in prod (swarm), so the first deploy is its
real test. Watch the deploy's migrate step.

## Implementation notes

Where the work went beyond the plan:

- **Bootstrap 1.4.0 requires `rxante/coding-standards ^3`.** Both apps moved
  from `^2`, which also brought in phpstan 2.3. phpstan 2.3 flags the
  "validate a collection with a typed closure" `array_map()` when its result
  is thrown away. In `ContactFormRecipients`, `Contact\Result`, auth's
  `ClientEntity`, and `Url`, the mapped result is now assigned to the
  property, as licenses.tjdraper.com does. Validation behavior and keys are
  unchanged.
- **The api pins `symfony/console` to `^7.4`.** The scoped update otherwise
  pulled in console 8, which removed `Application::add()`. Bootstrap 1.4.0's
  `addSymfonyCommand()` still calls it, and that's how the queue consumer and
  schedule runner commands are registered. Drop the pin once bootstrap calls
  `addCommand()`. Auth doesn't use `addSymfonyCommand()` and is on console 8.
- **PHP 8.5 deprecation in our own packages.** `buzzingpixel/php-queue`
  (`RedisDriver/NamespaceFactory.php`) and `buzzingpixel/php-scheduler`
  (`RedisDriver/RunSchedule.php`) call `ReflectionProperty::setAccessible()`,
  deprecated in 8.5. Prod only logs it. Dev's Whoops turns it into an
  exception, which broke the api CLI, queue consumer, and schedule runner in
  dev. Until both packages ship a release without that line,
  `api/config/LaggingPackageDeprecationFilter.php` swallows `E_DEPRECATED`
  from those two vendor paths only, registered in `api/cli` and
  `api/public/index.php` right after `Boot::start()`. The plan to fix the
  packages and remove the filter is in
  `~/Desktop/smrc-lagging-package-deprecations.md`.
