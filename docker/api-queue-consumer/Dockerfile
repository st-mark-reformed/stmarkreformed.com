ARG API_IMAGE=ghcr.io/st-mark-reformed/stmarkreformed.com-api
FROM $API_IMAGE

HEALTHCHECK --interval=60s --timeout=5s --start-period=30s --retries=3 \
    CMD bash /var/www/queueConsumerHealthcheck.sh

ENTRYPOINT []

# exec makes queueConsumer.sh PID 1 so it receives SIGTERM directly
CMD printenv | grep -v "no_proxy" >> /etc/environment && exec /var/www/queueConsumer.sh
