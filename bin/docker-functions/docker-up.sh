#!/usr/bin/env bash

function docker-up-help() {
    printf "(Brings Docker environment online)";
}

function docker-up() {
    docker compose ${composeFiles} -p stmark up -d;

    docker exec stmark-app bash -c "chmod -R 0777 /opt/project/public/imagecache;";
    docker exec stmark-app bash -c "chmod -R 0777 /opt/project/storage;";
    docker exec stmark-app bash -c "chmod -R 0777 /opt/project/public/uploads;";

    return 0;

}
