#!/usr/bin/env bash
set -euo pipefail

SERVICE="${1:-}"
if [ -z "$SERVICE" ]; then
    echo "Usage: $0 <api|auth>"
    exit 1
fi

SERVICE_NAME="smrc_${SERVICE}_migrations"
IMAGE="ghcr.io/st-mark-reformed/stmarkreformed.com-${SERVICE}"
NETWORK="smrc_default"
TIMEOUT_SECONDS=600
SLEEP_SECONDS=2
MAX_ATTEMPTS=5
RETRY_DELAY_SECONDS=10

ENV_FILES=(
    "/root/stmarkreformed.com/docker/${SERVICE}/.env"
    "/root/stmarkreformed.com/docker/${SERVICE}/.env.local"
)

COMMAND='php cli migrate:up'

cleanup() {
    docker service rm "$SERVICE_NAME" >/dev/null 2>&1 || true
}
trap cleanup EXIT

docker_args=(
    service create
    --detach
    --name "$SERVICE_NAME"
    --network "$NETWORK"
    --with-registry-auth
    --restart-condition none
    --constraint 'node.role == manager'
    --entrypoint ""
)

for env_file in "${ENV_FILES[@]}"; do
    docker_args+=(--env-file "$env_file")
done

docker_args+=(
    "$IMAGE"
    bash -c "$COMMAND"
)

# docker stack deploy returns before services finish restarting, so Redis or
# the database can briefly refuse connections while the migration boots. Retry
# a few times before treating the migration as failed.
run_migration() {
    docker "${docker_args[@]}" || return 1

    local start_time
    start_time=$(date +%s)

    while true; do
        local now elapsed task_id state message
        now=$(date +%s)
        elapsed=$((now - start_time))

        if [ "$elapsed" -ge "$TIMEOUT_SECONDS" ]; then
            echo "Timed out after ${TIMEOUT_SECONDS}s waiting for migration service."
            docker service logs "$SERVICE_NAME" || true
            exit 1
        fi

        task_id="$(docker service ps --quiet "$SERVICE_NAME" | head -n 1 || true)"

        if [ -z "$task_id" ]; then
            echo "Waiting for task to start..."
            sleep "$SLEEP_SECONDS"
            continue
        fi

        state="$(docker inspect "$task_id" --format '{{.Status.State}}' 2>/dev/null || true)"
        message="$(docker inspect "$task_id" --format '{{.Status.Err}}' 2>/dev/null || true)"

        echo "State: ${state:-unknown}${message:+ - $message}"

        case "$state" in
            complete)
                return 0
                ;;
            failed|rejected|shutdown)
                docker service logs "$SERVICE_NAME" || true
                return 1
                ;;
        esac

        sleep "$SLEEP_SECONDS"
    done
}

attempt=1

while true; do
    if run_migration; then
        echo "Migration completed successfully."
        exit 0
    fi

    if [ "$attempt" -ge "$MAX_ATTEMPTS" ]; then
        echo "Migration did not complete successfully after ${MAX_ATTEMPTS} attempts."
        exit 1
    fi

    echo "Migration attempt ${attempt} of ${MAX_ATTEMPTS} failed. Retrying in ${RETRY_DELAY_SECONDS}s..."
    cleanup
    attempt=$((attempt + 1))
    sleep "$RETRY_DELAY_SECONDS"
done
