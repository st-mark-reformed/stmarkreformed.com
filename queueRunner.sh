#!/usr/bin/env bash

# Run the queue every second infinitely
while true; do
    php /opt/project/craft queue/run;
    sleep 1;
done
