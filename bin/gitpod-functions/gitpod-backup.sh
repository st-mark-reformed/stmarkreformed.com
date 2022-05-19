#!/usr/bin/env bash

function gitpod-backup() {
    # Create directories
    mkdir -p /workspace/backup;
    rm -rf /workspace/backup
    mkdir -p /workspace/backup;

    docker-down;
    docker-up;

    # Backup the database
    docker exec stmark-db bash -c "mysqldump -usite -psecret site > /site.sql";
    docker cp stmark-db:/site.sql /workspace/backup/site.sql;
    docker exec stmark-db bash -c "rm /site.sql";

    # Backup files
    cd /workspace/stmarkreformed.com/public;
    zip -r /workspace/backup/files.zip files;

    # Backup uploads
    cd /workspace/stmarkreformed.com/public;
    zip -r /workspace/backup/uploads.zip uploads -x uploads/audio/\*;

    # Backup license key
    cp /workspace/stmarkreformed.com/config/license.key /workspace/backup/license.key;

    # Backup .ssh
    cd /workspace/stmarkreformed.com;
    zip -r /workspace/backup/.ssh.zip .ssh;

    # Backup .env.local
    cp /workspace/stmarkreformed.com/.env.local /workspace/backup/.env.local;

    return 0;
}
