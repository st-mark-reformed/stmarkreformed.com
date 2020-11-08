#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function node-bash() {
    if [[ "${isMacOs}" = "true" ]]; then
        docker run -it \
        -p 3000:3000 \
        -p 3001:3001 \
        -v ${PWD}:/app \
        -v stmark_node-modules-volume:/app/node_modules \
        -v stmark_yarn-cache-volume:/usr/local/share/.cache/yarn \
        -w /app \
        --network=proxy \
        ${nodeDockerImage} \
        bash;
    else
        docker run -it \
        -p 3000:3000 \
        -p 3001:3001 \
        -v ${PWD}:/app \
        -v stmark_yarn-cache-volume:/usr/local/share/.cache/yarn \
        -w /app \
        --network=proxy \
        ${nodeDockerImage} \
        bash;
    fi

    return 0;
}
