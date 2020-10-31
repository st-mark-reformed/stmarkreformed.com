#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function down() {
    docker kill stmark-bg-sync-node-modules;
    docker kill stmark-bg-sync-vendor;
    docker-compose ${composeFiles} -p stmark down;
    return 0;
}
