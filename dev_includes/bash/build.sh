#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function build() {
    docker-compose ${composeFiles} -p stmark build;

    return 0;
}
