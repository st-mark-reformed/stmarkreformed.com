#!/bin/sh
# $1 = env
# $2 = {{release}}
# $3 = {{project}}

# Run migrations
php craft migrate/up --interactive=0;

# Clear Caches
php craft cache/flush-all;

# Clear static cache
php craft craft-static/cache/purge;
