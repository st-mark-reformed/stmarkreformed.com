#!/usr/bin/env bash
set -euo pipefail

HEARTBEAT_FILE=/tmp/queue-consumer-heartbeat
MAX_STALENESS="${QUEUE_HEARTBEAT_MAX_STALENESS:-420}"

[ -f "$HEARTBEAT_FILE" ] || exit 1

[ $(( $(date +%s) - $(cat "$HEARTBEAT_FILE") )) -lt "$MAX_STALENESS" ]
