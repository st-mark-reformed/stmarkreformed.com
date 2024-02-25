#!/usr/bin/env bash

set -e;

php craft project-config/apply --interactive=0;
php craft migrate/all --interactive=0;
php craft cache/flush-all --interactive=0;
php craft clear-caches/compiled-templates --interactive=0;
php craft clear-caches/data --interactive=0;
php craft clear-caches/static-caches --interactive=0;
