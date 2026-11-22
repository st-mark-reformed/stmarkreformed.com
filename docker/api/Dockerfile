FROM rxantesystems/php-nginx:8.5.13-fpm-trixie-r1

# No Datadog agent runs on this host. The DD_* switches cover the processes
# that skip the image's init (migrations, queue consumer, schedule runner),
# since init is what applies ENABLE_DATADOG.
ENV ENABLE_DATADOG=0 \
    DD_TRACE_ENABLED=0 \
    DD_INSTRUMENTATION_TELEMETRY_ENABLED=0 \
    DD_TRACE_SIDECAR_TRACE_SENDER=0

# The image's server block plus the certbot webroot
COPY docker/api/nginx/default.template /etc/nginx/conf.d/default.template

# Copy the application into the container
COPY api /var/www

# Install composer dependencies
RUN composer install --no-interaction --no-ansi --no-progress --optimize-autoloader --no-dev

HEALTHCHECK --interval=5s --timeout=5s --start-period=10s --retries=10 CMD curl --fail http://localhost/healthcheck/659f105793f58 || exit 1
