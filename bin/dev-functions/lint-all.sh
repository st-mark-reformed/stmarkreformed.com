#!/usr/bin/env bash

function docker-lint-all() {
    set -e;

    docker-phpunit;
    docker-psalm;
    docker-phpstan;
    docker-phpcs;
    docker-php-cs-fixer-check;
    docker-eslint;
    docker-stylelint;

    return 0;
}

function dev-lint-all() {
    set -e;

    dev-phpunit;
    dev-psalm;
    dev-phpstan;
    dev-phpcs;
    dev-php-cs-fixer-check;
    dev-eslint;
    dev-stylelint;

    return 0;
}
