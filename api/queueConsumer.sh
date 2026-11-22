#!/usr/bin/env bash

HEARTBEAT_FILE=/tmp/queue-consumer-heartbeat
JOB_TIMEOUT="${QUEUE_JOB_TIMEOUT_SECONDS:-300}"

# This script runs as PID 1, which gets no default signal handling, so it
# handles SIGTERM itself: it exits at once between jobs and lets a running job
# finish first (until Docker's stop grace period runs out).
inJob=false
stopping=false

onStopSignal() {
    if [ "$inJob" = true ]; then
        stopping=true
        echo "queue-consumer: stop requested, finishing the current job first"
        return
    fi

    exit 0
}

trap onStopSignal TERM INT

# Bash runs a trap only after the foreground command finishes, so commands run
# in the background while the script waits; a signal interrupts the wait.
run() {
    "$@" &
    local child=$!
    local status

    wait "$child"
    status=$?

    # The trap interrupted the wait while the job continues
    while kill -0 "$child" 2> /dev/null; do
        wait "$child"
        status=$?
    done

    return "$status"
}

# Redis may still be starting or loading its data when this container starts
# (swarm ignores depends_on), so wait until it is ready before consuming
until run /usr/local/bin/php /var/www/cli queue:redis-ready-check > /dev/null 2>&1; do
    echo "queue-consumer: waiting for Redis to be ready…"
    run sleep 1
done

echo "Entering Queue Consume loop… (job timeout ${JOB_TIMEOUT}s)";

while true; do
    date +%s > "$HEARTBEAT_FILE"

    inJob=true
    run timeout -k 30 "$JOB_TIMEOUT" \
        /usr/local/bin/php /var/www/cli queue:consume-next --verbose --no-interaction
    exitCode=$?
    inJob=false

    if [ "$exitCode" -eq 124 ] || [ "$exitCode" -eq 137 ]; then
        echo "queue-consumer: job exceeded ${JOB_TIMEOUT}s and was killed (exit ${exitCode})" >&2
    fi

    if [ "$stopping" = true ]; then
        exit 0
    fi

    run sleep 5
done
