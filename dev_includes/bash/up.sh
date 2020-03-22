#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function up() {
    chmod -R 0777 storage;

    docker-compose ${composeFiles} -p stmark up -d;

    docker exec --user root stmark-php bash -c "chmod -R 0777 /opt/project/storage;";

    return 0;
}
