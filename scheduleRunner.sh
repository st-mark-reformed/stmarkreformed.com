#!/usr/bin/env bash

if [[ ! -f "/root/stmarkreformed.com/disableSchedule" ]]; then
    /usr/bin/docker exec -w /var/www stmark-app bash -c "XDEBUG_MODE=off php craft craft-scheduler/schedule/run --interactive=0";
fi
