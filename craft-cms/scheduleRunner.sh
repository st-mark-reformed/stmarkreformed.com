#!/usr/bin/env bash

echo "Running Craft Schedule"

/usr/local/bin/php -f /var/www/craft craft-scheduler/schedule/run --interactive=0
