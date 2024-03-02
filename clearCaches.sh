#!/usr/bin/env bash

echo "Clearing Craft Caches"

/usr/local/bin/php -f /var/www/craft clear-caches/compiled-templates --interactive=0
/usr/local/bin/php -f /var/www/craft clear-caches/data --interactive=0
/usr/local/bin/php -f /var/www/craft clear-caches/static-caches --interactive=0
