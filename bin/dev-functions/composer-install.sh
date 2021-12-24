#!/usr/bin/env bash

function docker-composer-install() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-dev-composer-install \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        registry.digitalocean.com/buzzingpixel/stmarkreformed.com-app bash -c "composer install";

    docker rm stmark-dev-composer-install >/dev/null 2>&1;

    return 0;
}
