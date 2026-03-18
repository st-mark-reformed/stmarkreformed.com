#!/bin/sh

# shellcheck disable=SC3028
# shellcheck disable=SC3054
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)";
DOCKER_DIR=$(dirname "${SCRIPT_DIR}");
PROJ_DIR=$(dirname "${DOCKER_DIR}");
AUTH_DIR="${PROJ_DIR}/auth";

docker run -it --rm \
    --entrypoint "" \
    --name api-provision \
    -v "${AUTH_DIR}:/var/www" \
    -w /var/www \
    ghcr.io/st-mark-reformed/stmarkreformed.com-auth sh -c "composer install";

cd ${AUTH_DIR} && pnpm install && npx @tailwindcss/cli -i ./assets/style.css -o ./public/assets/style.css

#docker run --rm \
#    --entrypoint "" \
#    --env NODE_ENV=development \
#    --env COREPACK_ENABLE_DOWNLOAD_PROMPT=0 \
#    --env CI=1 \
#    --name auth_provision \
#    --mount type=bind,source="${AUTH_DIR}",target=/app \
#    -w /app \
#    node:22 bash -c "npm install -g corepack@latest && corepack enable && pnpm install && npx @tailwindcss/cli -i ./assets/style.css -o ./public/assets/style.css";
