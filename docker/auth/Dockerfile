FROM node:22.23.5-slim AS frontend-builder
ENV PNPM_HOME="/pnpm"
ENV PATH="$PNPM_HOME:$PATH"
RUN npm install -g corepack@latest && corepack enable
ENV COREPACK_ENABLE_DOWNLOAD_PROMPT=0

WORKDIR /var/www

COPY ./auth /var/www

RUN pnpm install && npx @tailwindcss/cli -i ./assets/style.css -o ./public/assets/style.css

FROM rxantesystems/php-nginx:8.5.13-fpm-trixie-r1 AS application

# No Datadog agent runs on this host. The DD_* switches cover the processes
# that skip the image's init (migrations), since init is what applies
# ENABLE_DATADOG.
ENV ENABLE_DATADOG=0 \
    DD_TRACE_ENABLED=0 \
    DD_INSTRUMENTATION_TELEMETRY_ENABLED=0 \
    DD_TRACE_SIDECAR_TRACE_SENDER=0

# The image's server block plus the certbot webroot
COPY docker/auth/nginx/default.template /etc/nginx/conf.d/default.template

# Copy the application into the container
COPY auth /var/www

# Install composer dependencies
RUN composer install --no-interaction --no-ansi --no-progress --optimize-autoloader --no-dev

# Copy the front-end build
COPY --from=frontend-builder /var/www/public/assets /var/www/public/assets

HEALTHCHECK --interval=5s --timeout=5s --start-period=10s --retries=10 CMD curl --fail http://localhost/healthcheck/659f105793f58 || exit 1
