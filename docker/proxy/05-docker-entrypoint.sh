#!/usr/bin/env sh
set -eu
envsubst '\$WEB_PROXY `$CRAFT_PROXY' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
exec "$@"
