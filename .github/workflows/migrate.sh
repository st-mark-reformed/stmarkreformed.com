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

docker "${docker_args[@]}"

start_time=$(date +%s)

while true; do
    now=$(date +%s)
    elapsed=$((now - start_time))

    if [ "$elapsed" -ge "$TIMEOUT_SECONDS" ]; then
        echo "Timed out after ${TIMEOUT_SECONDS}s waiting for migration service."
        docker service logs "$SERVICE_NAME" || true
        exit 1
    fi

    task_id="$(docker service ps --quiet "$SERVICE_NAME" | head -n 1 || true)"

    if [ -n "$task_id" ]; then
        state="$(docker inspect "$task_id" --format '{{.Status.State}}' 2>/dev/null || true)"
        message="$(docker inspect "$task_id" --format '{{.Status.Err}}' 2>/dev/null || true)"

        echo "State: ${state:-unknown}${message:+ - $message}"

        case "$state" in
            complete)
                echo "Migration completed successfully."
                exit 0
                ;;
            failed|rejected|shutdown)
                echo "Migration did not complete successfully."
                docker service logs "$SERVICE_NAME" || true
                exit 1
                ;;
        esac
    else
        echo "Waiting for task to start..."
    fi

    sleep "$SLEEP_SECONDS"
done
