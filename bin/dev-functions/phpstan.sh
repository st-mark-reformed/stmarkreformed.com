#!/usr/bin/env bash

function docker-phpstan() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run --rm ${interactiveArgs} \
        --entrypoint "" \
        --name stmark-phpstan \
        -v ${PWD}:/opt/project \
        -w /opt/project \
        --env ENABLE_PHP_DEV_CONFIG=1 \
        --env ENABLE_XDEBUG=1 \
        --env DISABLE_PHP_FPM=1 \
        --env DISABLE_NGINX=1 \
        ghcr.io/st-mark-reformed/stmarkreformed.com-app bash -c "XDEBUG_MODE=off php -d memory_limit=4G ./vendor/bin/phpstan analyse CraftFrontController.php public/index.php config src";

    return 0;
}

function dev-phpstan() {
    XDEBUG_MODE=off /usr/local/bin/php80 -d memory_limit=4G ./vendor/bin/phpstan analyse CraftFrontController.php public/index.php config src;

    return 0;
}
