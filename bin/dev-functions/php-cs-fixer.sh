#!/usr/bin/env bash

function docker-php-cs-fixer-check() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-phpcsfixer \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        ghcr.io/st-mark-reformed/stmarkreformed.com-app bash -c "XDEBUG_MODE=off ./vendor/bin/php-cs-fixer fix -v --dry-run --stop-on-violation --using-cache=no";

    docker rm stmark-phpcsfixer >/dev/null 2>&1;

    return 0;
}

function dev-php-cs-fixer-check() {
    XDEBUG_MODE=off /usr/local/bin/php80 ./vendor/bin/php-cs-fixer fix -v --dry-run --stop-on-violation --using-cache=no;

    return 0;
}

function docker-php-cs-fixer-fix() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-phpcsfixer \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        ghcr.io/st-mark-reformed/stmarkreformed.com-app bash -c "XDEBUG_MODE=off ./vendor/bin/php-cs-fixer fix -v --using-cache=no";

    docker rm stmark-phpcsfixer >/dev/null 2>&1;

    return 0;
}

function dev-php-cs-fixer-fix() {
    XDEBUG_MODE=off /usr/local/bin/php80 ./vendor/bin/php-cs-fixer fix -v --using-cache=no;

    return 0;
}
