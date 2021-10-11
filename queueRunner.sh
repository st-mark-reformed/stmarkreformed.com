#!/usr/bin/env bash

# Run the queue every second infinitely
while true; do
    /usr/bin/docker exec -w /opt/project stmark-app bash -c "XDEBUG_MODE=off php /opt/project/craft queue/run --interactive=0";
    sleep 1;
done

