ARG API_IMAGE=ghcr.io/st-mark-reformed/stmarkreformed.com-api
FROM $API_IMAGE

HEALTHCHECK --interval=60s --timeout=5s --start-period=30s --retries=3 \
    CMD bash /var/www/queueConsumerHealthcheck.sh

ENTRYPOINT []

CMD printenv | grep -v "no_proxy" >> /etc/environment && /var/www/queueConsumer.sh
