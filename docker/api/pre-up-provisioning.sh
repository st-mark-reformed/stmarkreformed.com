#!/bin/sh

# shellcheck disable=SC3028
# shellcheck disable=SC3054
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)";
DOCKER_DIR=$(dirname "${SCRIPT_DIR}");
PROJ_DIR=$(dirname "${DOCKER_DIR}");
API_DIR="${PROJ_DIR}/api";

TTY_FLAGS="";
[ -t 0 ] && TTY_FLAGS="-it";

docker run $TTY_FLAGS --rm \
    --entrypoint "" \
    --name api-provision \
    -v "${API_DIR}:/var/www" \
    -w /var/www \
    ghcr.io/st-mark-reformed/stmarkreformed.com-api sh -c "composer install";
