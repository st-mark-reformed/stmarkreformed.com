#!/bin/sh

# shellcheck disable=SC3028
# shellcheck disable=SC3054
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)";
DOCKER_DIR=$(dirname "${SCRIPT_DIR}");
PROJ_DIR=$(dirname "${DOCKER_DIR}");
APP_DIR="${PROJ_DIR}/craft-cms";

docker run -it --rm \
    --entrypoint "" \
    --name app-provision \
    -v "${APP_DIR}:/var/www" \
    -w /var/www \
    ghcr.io/st-mark-reformed/stmarkreformed.com-app sh -c "composer install";

cd ${APP_DIR};
yarn install;
yarn css;
yarn javascript;
