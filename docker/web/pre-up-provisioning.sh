#!/bin/sh

# shellcheck disable=SC3028
# shellcheck disable=SC3054
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)";
DOCKER_DIR=$(dirname "${SCRIPT_DIR}");
PROJ_DIR=$(dirname "${DOCKER_DIR}");
WEB_DIR="${PROJ_DIR}/web";
NEXT_DIR="${WEB_DIR}/.next";

docker run --rm \
    --entrypoint "" \
    --env NODE_ENV=development \
    --name web_provision \
    --mount type=bind,source="${WEB_DIR}",target=/app \
    -w /app \
    ghcr.io/st-mark-reformed/stmarkreformed.com-web bash -c "pnpm install";

if [ ! -d "${NEXT_DIR}" ]; then
    echo "Running local next build...";

    docker run -it --rm \
        --entrypoint "" \
        --name web_provision \
        --mount type=bind,source="${WEB_DIR}",target=/app \
        -w /app \
        ghcr.io/st-mark-reformed/stmarkreformed.com-web bash -c "pnpm build";
else
    echo 'Local next build already exists. If the web container fails to run, ./dev docker container web-node "pnpm build"';
fi
