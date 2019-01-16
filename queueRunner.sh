#!/bin/bash

################################################################################
## About this script
# Continuously runs the craft queue until the process is stopped.
# This script should be placed one level up from project and run by supervisor

## Supervisor commmands
# sudo supervisorctl (places user in the supervisor command line)
#     stop name_of_my_running_script
#     start name_of_my_running_script
# sudo service supervisor restart (does not re-read config)
# sudo supervisorctl reread (reads config)
# sudo supervisorctl update (after rereading config above, restarts apps whos config has changed)
# Supervisor config location: /etc/supervisor/conf.d/name_of_my_running_script.conf

## Config file example
# [program:name_of_my_running_script]
# command=/path/to/script.sh
# autostart=true
# autorestart=true
# stderr_logfile=/var/log/name_of_my_running_script.err.log
# stdout_logfile=/var/log/name_of_my_running_script.out.log
################################################################################

BASEDIR=$(dirname "$0");

# Run the queue every second infinitely
while true; do
    sudo chmod -R 0777 ${BASEDIR}/storage/public/uploads;
    sudo chmod -R 0777 ${BASEDIR}/storage/storage;
    sudo chmod -R 0777 ${BASEDIR}/current/public/cache;
    sudo chmod -R 0777 ${BASEDIR}/current/public/cpresources;
    php ${BASEDIR}/current/craft queue/run;
    sleep 1;
done
