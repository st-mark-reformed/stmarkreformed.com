#!/usr/bin/env bash

source ../../dev 2> /dev/null;

function docker-sync-help() {
    printf "(Syncs production database and content to local environment)";
}

function docker-sync() {
    docker-compose -f docker-compose.sync.to.local.yml -p stmark-ssh up -d;

    docker exec stmark-ssh bash -c "chmod +x /opt/project/docker/scripts/sync-from-prod-01-ssh.sh;";
    docker exec stmark-ssh bash -c "/opt/project/docker/scripts/sync-from-prod-01-ssh.sh;";

    docker exec stmark-db bash -c "chmod +x /opt/project/docker/scripts/sync-from-prod-02-db.sh;";
    docker exec stmark-db bash -c "/opt/project/docker/scripts/sync-from-prod-02-db.sh;";

    docker exec stmark-ssh bash -c "chmod +x /opt/project/docker/scripts/sync-from-prod-03-rsync.sh;";
    docker exec stmark-ssh bash -c "/opt/project/docker/scripts/sync-from-prod-03-rsync.sh;";

    docker-compose -f docker-compose.sync.to.local.yml -p stmark-ssh down;

    return 0;
}
