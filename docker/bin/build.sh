#!/usr/bin/env bash

#########################
# Handy Color Variables #
#########################

# Reset
Reset="\033[0m"; # Text Reset

# Regular Colors
Black="\033[0;30m"; # Black
Red="\033[0;31m"; # Red
Green="\033[0;32m"; # Green
Yellow="\033[0;33m"; # Yellow
Blue="\033[0;34m"; # Blue
Purple="\033[0;35m"; # Purple
Cyan="\033[0;36m"; # Cyan
White="\033[0;37m"; # White

# Bold
BBlack="\033[1;30m"; # Black
BRed="\033[1;31m"; # Red
BGreen="\033[1;32m"; # Green
BYellow="\033[1;33m"; # Yellow
BBlue="\033[1;34m"; # Blue
BPurple="\033[1;35m"; # Purple
BCyan="\033[1;36m"; # Cyan
BWhite="\033[1;37m"; # White

##############################
# /END Handy Color Variables #
##############################

TAG=${1};

if [[ "${TAG}" = "" ]]; then
    TAG="latest";
fi

set -e;

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )";

cd ${SCRIPT_DIR};

printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-app${Reset}\n";

docker build ../../ \
    --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-app:cache \
    --build-arg BUILDKIT_INLINE_CACHE=1 \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-app:"${TAG}" \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-app:latest \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-app:cache \
    --file ../application/Dockerfile

printf "${Green}Finished ghcr.io/st-mark-reformed/stmarkreformed.com-app${Reset}\n\n";

printf "${Cyan}Building ghcr.io/st-mark-reformed/stmarkreformed.com-db${Reset}\n";

docker build ../../ \
    --cache-from ghcr.io/st-mark-reformed/stmarkreformed.com-db:cache \
    --build-arg BUILDKIT_INLINE_CACHE=1 \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-db:"${TAG}" \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-db:latest \
    --tag ghcr.io/st-mark-reformed/stmarkreformed.com-db:cache \
    --file ../db/Dockerfile

printf "${Green}Finished ghcr.io/st-mark-reformed/stmarkreformed.com-db${Reset}\n\n";

