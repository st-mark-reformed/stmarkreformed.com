#!/usr/bin/env bash

function container-node-help() {
    printf "[some_command] (Execute command in \`node\` image. Empty argument starts a bash session)";
}

function container-node() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    if [[ -z "${allArgsExceptFirst}" ]]; then
        printf "${Yellow}Remember to 'exit' when you're done.${Reset}\n";
        docker run ${interactiveArgs} \
            --name stmark-node \
            -v ${PWD}:/app \
            -w /app \
            ${nodeDockerImage} bash;
    else
        docker run ${interactiveArgs} \
            --name stmark-node \
            -v ${PWD}:/app \
            -w /app \
            ${nodeDockerImage} bash -c "${allArgsExceptFirst}";
    fi

    docker rm stmark-node >/dev/null 2>&1;

    return 0;
}
