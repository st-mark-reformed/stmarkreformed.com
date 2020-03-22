#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function down() {
    docker-compose ${composeFiles} -p stmark down;

    return 0;
}
