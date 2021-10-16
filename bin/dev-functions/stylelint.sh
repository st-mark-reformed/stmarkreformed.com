#!/usr/bin/env bash

function docker-stylelint() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-eslint \
        -v ${PWD}:/app \
        -w /app \
        ${nodeDockerImage} bash -c 'yarn stylelint --allow-empty-input "assets/**/*.{css,pcss,html,twig}" "src/**/*.{css,pcss,html,twig}"';

    docker rm stmark-eslint >/dev/null 2>&1;

    return 0;
}

function docker-stylelint-fix() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    docker run ${interactiveArgs} \
        --name stmark-eslint \
        -v ${PWD}:/app \
        -w /app \
        ${nodeDockerImage} bash -c 'yarn stylelint --fix --allow-empty-input "assets/**/*.{css,pcss,html,twig}" "src/**/*.{css,pcss,html,twig}"';

    docker rm stmark-eslint >/dev/null 2>&1;

    return 0;
}

function dev-stylelint() {
    yarn stylelint --allow-empty-input "assets/**/*.{css,pcss,html,twig}" "src/**/*.{css,pcss,html,twig}";

    return 0;
}

function dev-stylelint-fix() {
    yarn stylelint --allow-empty-input "assets/**/*.{css,pcss,html,twig}" "src/**/*.{css,pcss,html,twig}";

    return 0;
}
