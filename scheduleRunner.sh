#!/usr/bin/env bash

if [[ ! -f "/root/stmarkreformed.com/disableSchedule" ]]; then
    echo 'here';
    # /usr/bin/docker exec -w /opt/project stmark-app bash -c "XDEBUG_MODE=off php craft craft-scheduler/schedule/run --interactive=0";
fi
