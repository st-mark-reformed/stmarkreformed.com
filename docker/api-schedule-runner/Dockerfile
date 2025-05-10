ARG API_IMAGE=ghcr.io/st-mark-reformed/stmarkreformed.com-api
FROM $API_IMAGE

RUN apt-get update && apt-get install -y --no-install-recommends cron \
    # Cleanup
    && rm -rf /var/lib/apt/lists/*

RUN touch /var/log/cron.log

RUN chmod +x /var/www/scheduleRunner.sh

ENV TZ="America/Chicago"

RUN echo "America/Chicago" > /etc/timezone

RUN (crontab -l \
    ; echo "* * * * * /var/www/scheduleRunner.sh >> /var/log/cron.log 2>&1" \
) | crontab

ENTRYPOINT []

HEALTHCHECK CMD php -v

CMD ["sh", "-c", "printenv | grep -v 'no_proxy' >> /etc/environment && cron && tail -f /var/log/cron.log"]
