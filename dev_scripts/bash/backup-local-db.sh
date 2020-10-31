#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function backup-local-db() {
    DATE_DISPLAY=$(date +%Y-%m-%d--%H-%M-%S);

    # Make sure dump file does not exist in container
    docker exec --user root --workdir /tmp stmark-db bash -c '[[ -e db_backup.sql ]] && rm db_backup.sql';

    # Dump database in Docker container
    docker exec --user root --workdir /tmp stmark-db bash -c 'mysqldump -ustmark -psecret stmark > db_backup.sql';

    # Copy dump out of container
    mkdir -p docker/localStorage/db-backups;
    docker cp stmark-db:/tmp/db_backup.sql docker/localStorage/db-backups/db_backup__"${DATE_DISPLAY}".sql;

    # Delete the dump from the container
    docker exec --user root --workdir /tmp stmark-db bash -c '[[ -e db_backup.sql ]] && rm db_backup.sql';

    return 0;
}
