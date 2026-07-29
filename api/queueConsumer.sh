#!/usr/bin/env bash

HEARTBEAT_FILE=/tmp/queue-consumer-heartbeat
JOB_TIMEOUT="${QUEUE_JOB_TIMEOUT_SECONDS:-300}"

echo "Entering Queue Consume loop… (job timeout ${JOB_TIMEOUT}s)";

while true; do
    date +%s > "$HEARTBEAT_FILE"

    timeout -k 30 "$JOB_TIMEOUT" \
        /usr/local/bin/php /var/www/cli queue:consume-next --verbose --no-interaction
    exitCode=$?

    if [ "$exitCode" -eq 124 ] || [ "$exitCode" -eq 137 ]; then
        echo "queue-consumer: job exceeded ${JOB_TIMEOUT}s and was killed (exit ${exitCode})" >&2
    fi

    sleep 5
done
