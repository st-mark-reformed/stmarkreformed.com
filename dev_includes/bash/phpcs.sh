#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function phpcs() {
    vendor/bin/phpcs --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcs dev public/index.php config; vendor/bin/php-cs-fixer fix --verbose --dry-run --using-cache=no;

    return 0;
}

function phpcbf() {
    vendor/bin/phpcbf --config-set installed_paths ../../doctrine/coding-standard/lib,../../slevomat/coding-standard; vendor/bin/phpcbf dev public/index.php config; vendor/bin/php-cs-fixer fix --verbose --using-cache=no;

    return 0;
}
