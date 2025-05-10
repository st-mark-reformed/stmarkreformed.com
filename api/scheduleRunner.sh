#!/usr/bin/env bash

echo "Running API Schedule"

/usr/local/bin/php -f /var/www/cli schedule:run --verbose --no-interaction
