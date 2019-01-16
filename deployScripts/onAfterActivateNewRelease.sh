#!/bin/sh
# $1 = env
# $2 = {{release}}
# $3 = {{project}}

# Run migrations
php ${2}/craft migrate/up --interactive=0

# Clear the cache
php ${2}/craft cache/flush-all --interactive=0
php ${2}/craft craft-static/cache/purge --interactive=0
