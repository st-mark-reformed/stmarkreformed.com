#!/usr/bin/env bash

composerDockerImage="composer:1.3.2";

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

cmd=${1};
allArgs=${@};
allArgsExceptFirst=${@:2};
secondArg=${2};

isMacOs=false;
composeFiles="-f docker-compose.yml -f docker-compose.dev.yml";

if [[ "$(uname)" = "Darwin" ]] || [[ "$(uname)" = "darwin" ]]; then
    isMacOs=true;
    composeFiles="-f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.dev.sync.yml";
fi

for f in dev_includes/bash/*; do
    chmod +x ${f};
    source ${f};
done

if [[ -z "${cmd}" ]]; then
    printf "${Green}The following commands are available:\n${Yellow}";

    IFS=$'\n'
    for f in $(declare -F); do
       printf "  ./dev ${f:11}\n";
    done

    printf "${Reset}";

    exit 0;
fi

${cmd} ${allArgsExceptFirst};
