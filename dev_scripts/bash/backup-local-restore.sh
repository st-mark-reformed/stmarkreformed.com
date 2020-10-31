#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function backup-local-restore() {
    FILENAME=${secondArg};

    if [[ -z "${FILENAME}" ]]; then
        echo "Please specify a filename";

        return 1;
    fi

    FILEPATH="db-backups/${FILENAME}";

    if [[ ! -f "${FILEPATH}" ]]; then
        echo "File does not exist";

        return 1;
    fi

    # Make sure dump file does not exist in container
    docker exec --user root --workdir /tmp stmark-db bash -c '[[ -e db_restore.sql ]] && rm db_restore.sql';

    # Copy dump file to container
    docker cp "${FILEPATH}" stmark-db:/tmp/db_restore.sql;

    # Clear local database in prep for new dump
    docker exec --user root --workdir /tmp stmark-db bash -c "mysqldump -ustmark -psecret --add-drop-table --no-data stmark | grep ^DROP | mysql --init-command=\"SET SESSION FOREIGN_KEY_CHECKS=0;\" -ustmark -psecret stmark --force";

    # Import database dump into local database
    docker exec --user root --workdir /tmp stmark-db bash -c "mysql -ustmark -psecret stmark < db_restore.sql";

    # Delete the dump from the container
    docker exec --user root --workdir /tmp stmark-db bash -c '[[ -e db_restore.sql ]] && rm db_restore.sql';

    return 0;
}
