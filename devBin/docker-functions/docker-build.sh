#!/usr/bin/env bash

function docker-build-help() {
    printf "(Build the Docker images for this project)";
}

function docker-build() {
    set -e;

    WORK_DIR="$(cd "$(dirname "$0")" >/dev/null 2>&1 && pwd)";

    # Run the proxy build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-proxy${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-proxy \
        --file docker/proxy/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-proxy \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-proxy${Reset}\n\n";

    # Run the web build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-web${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-web \
        --file docker/web/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-web \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-web${Reset}\n\n";

    # Run the app build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-app${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-app \
        --file docker/application/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-app \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-app${Reset}\n\n";

    # Run the schedule-runner build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-app-schedule-runner${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --file docker/schedule-runner/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-app-schedule-runner \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-app-schedule-runner${Reset}\n\n";

    # Run the db build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-db${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-db \
        --file docker/db/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-db \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-db${Reset}\n\n";

    # Run the utility build
    printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-utility${Reset}\n";
    DOCKER_BUILDKIT=1 docker build \
        --build-arg BUILDKIT_INLINE_CACHE=1 \
        --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-utility \
        --file docker/utility/Dockerfile \
        --tag ghcr.io/st-mark-reformed/stmarkreformed.com-utility \
        ${WORK_DIR};
    printf "${Green}Finished building ghcr.io/st-mark-reformed/stmarkreformed.com-utility${Reset}\n\n";

    return 0;
}
