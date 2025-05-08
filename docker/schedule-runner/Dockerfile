ARG APP_IMAGE=ghcr.io/st-mark-reformed/stmarkreformed.com-app
FROM $APP_IMAGE
ARG TARGETPLATFORM
ARG TARGETARCH
ARG BUILDPLATFORM
ARG BUILDARCH

RUN apt-get update && apt-get install -y --no-install-recommends cron \
    # Cleanup
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN touch /var/log/cron.log

ENV TZ="America/Chicago"

RUN echo "America/Chicago" > /etc/timezone

RUN (crontab -l ; echo "* * * * * /var/www/scheduleRunner.sh >> /var/log/cron.log 2>&1") | crontab

RUN (crontab -l ; echo "0 8 * * * /var/www/clearCaches.sh >> /var/log/cron.log 2>&1") | crontab

RUN (crontab -l ; echo "*/5 * * * * /var/www/cleanImapAttachmentsDir.sh >> /var/log/cron.log 2>&1") | crontab

ENTRYPOINT []

CMD printenv | grep -v "no_proxy" >> /etc/environment && cron && tail -f /var/log/cron.log
