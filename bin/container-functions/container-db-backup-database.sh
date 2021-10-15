#!/usr/bin/env bash

function container-db-backup-database-help() {
    printf "(Backs up the database to a file)";
}

function container-db-backup-database() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    DATE=$(date +"%Y-%m-%d__%H-%M-%S");

    # Make sure dump file does not exist in container
    docker exec ${interactiveArgs} stmark-db bash -c '[[ -e /dump.sql ]] && rm /dump.sql';

    # Dump database in Docker container
    docker exec ${interactiveArgs} stmark-db bash -c 'mysqldump -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE} > /dump.sql';

    # Copy dump out of container
    mkdir -p docker/localStorage/dbBackups;
    docker cp stmark-db:/dump.sql docker/localStorage/dbBackups/${DATE}.sql;

    # Delete the dump from the container
    docker exec ${interactiveArgs} stmark-db bash -c '[[ -e /dump.sql ]] && rm /dump.sql';

    return 0;
}
