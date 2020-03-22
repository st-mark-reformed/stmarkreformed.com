#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function cli() {
    docker exec -it --user root --workdir /opt/project stmark-php bash -c "php craft ${allArgsExceptFirst}";

    return 0;
}
