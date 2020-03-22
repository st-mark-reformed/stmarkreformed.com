#!/usr/bin/env bash

source ../../dev.sh 2> /dev/null;

function sync() {
    chmod +x scripts/dev/ensure-ssh-keys-working.sh;

    docker-compose -f docker-composer.sync.dev.to.local.yml -p stmarkssh up -d;

    docker exec stmark-ssh bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-01-ssh.sh; /opt/project/scripts/dev/sync-to-local-01-ssh.sh;";

    docker exec stmark-db bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-02-db.sh; /opt/project/scripts/dev/sync-to-local-02-db.sh;";

    docker exec stmark-ssh bash -c "chmod +x /opt/project/scripts/dev/sync-to-local-03-rsync.sh; /opt/project/scripts/dev/sync-to-local-03-rsync.sh;";

    docker-compose -f docker-composer.sync.dev.to.local.yml -p stmarkssh down;

    return 0;
}
