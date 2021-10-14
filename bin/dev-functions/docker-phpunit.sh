#!/usr/bin/env bash

function container-node-help() {
    printf "[some_command] (Execute command in \`node\` image. Empty argument starts a bash session)";
}

function docker-phpunit() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-phpunit \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        registry.digitalocean.com/buzzingpixel/stmarkreformed.com-app bash -c "XDEBUG_MODE=coverage ./vendor/bin/phpunit";

    docker rm stmark-phpunit >/dev/null 2>&1;

    return 0;
}

function dev-phpunit() {
    XDEBUG_MODE=coverage /usr/local/bin/php80 ./vendor/bin/phpunit

    return 0;
}
