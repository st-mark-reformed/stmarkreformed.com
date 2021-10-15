#!/usr/bin/env bash

function container-db-restore-from-backup-help() {
    printf "[backup-name.sql] (Restores database from sql backup file)";
}

function container-db-restore-from-backup() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    if [[ -z "${secondArg}" ]]; then
        printf "${Red}You must provide the backup name as the second argument${Reset}\n";

        exit;
    fi

    if [[ ! -f "docker/localStorage/dbBackups/${secondArg}" ]]; then
        printf "${Red}The specified file does not exist in docker/localStorage/dbBackups${Reset}\n";

        exit;
    fi

    # Make sure dump file does not exist in container
    docker exec ${interactiveArgs} stmark-db bash -c '[[ -e /dump.sql ]] && rm /dump.sql';

    # Copy dump file to container
    docker cp docker/localStorage/dbBackups/${secondArg} stmark-db:/dump.sql;

    # Clear local database in prep for new dump
    docker exec ${interactiveArgs} stmark-db bash -c 'mysqldump -u${DB_USER} -p${DB_PASSWORD} --add-drop-table --no-data ${DB_DATABASE} | grep ^DROP | mysql --init-command=\"SET SESSION FOREIGN_KEY_CHECKS=0;\" -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE} --force';

    # Import database dump into local database
    docker exec ${interactiveArgs} stmark-db bash -c 'mysql -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE} < /dump.sql';

    # Delete the dump from the container
    docker exec ${interactiveArgs} stmark-db bash -c '[[ -e /dump.sql ]] && rm /dump.sql';

    return 0;
}
