#!/usr/bin/env bash

function docker-compose-config-help() {
    printf "(Displays the docker-compose config)";
}

function docker-compose-config() {
    docker compose ${composeFiles} -p stmark config;

    return 0;
}
