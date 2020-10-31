#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function sync-from-prod() {
    docker-compose -f docker-compose.sync.to.local.yml -p stmark-ssh up -d;
    docker exec stmark-ssh bash -c "chmod +x /opt/project/dev_scripts/docker/ensure-ssh-keys-working.sh;";

    docker exec stmark-ssh bash -c "chmod +x /opt/project/dev_scripts/docker/sync-from-prod-01-ssh.sh;";
    docker exec stmark-ssh bash -c "/opt/project/dev_scripts/docker/sync-from-prod-01-ssh.sh;";

    docker exec stmark-db bash -c "chmod +x /opt/project/dev_scripts/docker/sync-from-prod-02-db.sh;";
    docker exec stmark-db bash -c "/opt/project/dev_scripts/docker/sync-from-prod-02-db.sh;";

    docker exec stmark-ssh bash -c "chmod +x /opt/project/dev_scripts/docker/sync-from-prod-03-rsync.sh;";
    docker exec stmark-ssh bash -c "/opt/project/dev_scripts/docker/sync-from-prod-03-rsync.sh;";

    docker-compose -f docker-compose.sync.to.local.yml -p stmark-ssh down;

    return 0;
}
