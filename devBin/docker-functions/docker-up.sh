#!/usr/bin/env bash

function docker-up-help() {
    printf "(Brings Docker environment online)";
}

function docker-up() {
    docker network create traefik-dev_default >/dev/null 2>&1;

    docker compose -f docker/docker-compose.dev.yml -p stmark up -d;

    docker exec stmark-app bash -c "chmod 0755 /var/www";

    docker exec stmark-app bash -c "chmod -R 0777 /var/www/config/project";
    docker exec stmark-app bash -c "chmod -R 0777 /var/www/public/cpresources;";
    docker exec stmark-app bash -c "chmod -R 0777 /var/www/public/imagecache;";
    docker exec stmark-app bash -c "chmod -R 0777 /var/www/public/uploads;";
    docker exec stmark-app bash -c "chmod -R 0777 /var/www/storage;";

    return 0;
}
