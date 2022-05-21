#!/usr/bin/env bash

function docker-eslint() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run --rm ${interactiveArgs} \
        --name stmark-eslint \
        -v ${PWD}:/app \
        -w /app \
        ${nodeDockerImage} bash -c 'yarn eslint --ext .js --ext .ts --ext .jsx --ext .tsx --ext .html --ext .vue --ext .mjs --ext .twig --no-error-on-unmatched-pattern assets src';

    return 0;
}

function dev-eslint() {
    yarn eslint --ext .js --ext .ts --ext .jsx --ext .tsx --ext .html --ext .vue --ext .mjs --ext .twig --no-error-on-unmatched-pattern assets src;

    return 0;
}
