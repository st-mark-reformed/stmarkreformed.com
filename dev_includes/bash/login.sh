#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function login() {
    docker exec -it --user root --workdir /opt/project stmark-${secondArg} bash;

    return 0;
}
