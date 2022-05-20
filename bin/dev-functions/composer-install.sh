#!/usr/bin/env bash

function docker-composer-install() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run --rm \
        ${interactiveArgs} \
        --entrypoint "" \
        --name stmark-dev-composer-install \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        ghcr.io/st-mark-reformed/stmarkreformed.com-app bash -c "composer install";

    return 0;
}
