#!/usr/bin/env bash

function gitpod-restore-from-backup() {
    FILE_EXISTS=false;

    if [[ -f "/workspace/backup/uploads.zip" ]]; then
        FILE_EXISTS=true;
    fi

    if [[ "${FILE_EXISTS}" = "false" ]]; then
        echo 'Backup does not exist';

        return 1;
    fi

    docker-down;

    # Restore .env.local
    touch /workspace/stmarkreformed.com/.env.local;
    rm /workspace/stmarkreformed.com/.env.local;
    cp /workspace/backup/.env.local /workspace/stmarkreformed.com/.env.local;

    # Restore .ssh
    rm -rf /workspace/stmarkreformed.com/.ssh;
    cp /workspace/backup/.ssh.zip /workspace/stmarkreformed.com/.ssh.zip;
    cd /workspace/stmarkreformed.com;
    unzip .ssh.zip;
    rm .ssh.zip;

    # Restore license.key
    touch /workspace/stmarkreformed.com/config/license.key;
    rm /workspace/stmarkreformed.com/config/license.key;
    cp /workspace/backup/license.key /workspace/stmarkreformed.com/config/license.key;

    # Restore uploads
    rm -rf /workspace/stmarkreformed.com/public/uploads;
    cp /workspace/backup/uploads.zip /workspace/stmarkreformed.com/public/uploads.zip;
    cd /workspace/stmarkreformed.com/public;
    unzip uploads.zip;
    rm uploads.zip;

    # Restore files
    rm -rf /workspace/stmarkreformed.com/public/files;
    cp /workspace/backup/files.zip /workspace/stmarkreformed.com/public/files.zip;
    cd /workspace/stmarkreformed.com/public;
    unzip files.zip;
    rm files.zip;

    cd /workspace/stmarkreformed.com;

    docker-up;

    # Restore the database
    docker cp /workspace/backup/site.sql ansel-db:/site.sql;
    docker exec stmark-db bash -c "mysql -usite -psecret site < /site.sql";
    docker exec stmark-db bash -c "rm /site.sql";

    cd /workspace/stmarkreformed.com;

    return 0;
}
