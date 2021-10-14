#!/usr/bin/env bash

function docker-phpcs() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-psalm \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        registry.digitalocean.com/buzzingpixel/stmarkreformed.com-app bash -c "php -d memory_limit=4G ./vendor/bin/phpcs";

    docker rm stmark-psalm >/dev/null 2>&1;

    return 0;
}

function dev-phpcs() {
    /usr/local/bin/php80 -d memory_limit=4G ./vendor/bin/phpcs

    return 0;
}

function docker-phpcbf() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-psalm \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        registry.digitalocean.com/buzzingpixel/stmarkreformed.com-app bash -c "php -d memory_limit=4G ./vendor/bin/phpcbf";

    docker rm stmark-psalm >/dev/null 2>&1;

    return 0;
}

function dev-phpcbf() {
    /usr/local/bin/php80 -d memory_limit=4G ./vendor/bin/phpcbf

    return 0;
}
