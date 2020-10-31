#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function up() {
    docker network create proxy >/dev/null 2>&1;

    COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose ${composeFiles} -p stmark up -d;

    docker exec --user root stmark-php bash -c "chmod -R 0777 /opt/project/public/imagecache;";
    docker exec --user root stmark-php bash -c "chmod -R 0777 /opt/project/storage;";
    docker exec --user root stmark-php bash -c "chmod -R 0777 /opt/project/public/uploads;";

    return 0;
}
