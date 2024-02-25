#!/usr/bin/env bash

function container-node-help() {
    printf "[some_command] (Execute command in \`node\` image. Empty argument starts a bash session)";
}

function container-node() {
    if [[ -z "${allArgsExceptFirst}" ]]; then
        printf "${Yellow}Remember to 'exit' when you're done.${Reset}\n";
        docker run -it --rm \
            --name stmark-node \
            -v ${PWD}:/app \
            -w /app \
            node:16.10.0 bash;
    else
        docker run -it --rm \
            --name stmark-node \
            -v ${PWD}:/app \
            -w /app \
            node:16.10.0 bash -c "${allArgsExceptFirst}";
    fi

    docker rm stmark-node >/dev/null 2>&1;

    return 0;
}
