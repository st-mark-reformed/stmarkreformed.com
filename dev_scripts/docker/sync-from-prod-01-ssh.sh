#!/usr/bin/env bash

SERVER_USER="root";
SERVER_ADDRESS="206.81.13.32";
SQL_FILE_NAME="stmark_prod.sql";
REMOTE_PROJECT_PATH="/root/stmarkreformed.com";
REMOTE_SQL_PATH="${REMOTE_PROJECT_PATH}/${SQL_FILE_NAME}";
DB_NAME="stmark";
DB_USER="stmark";
DB_CONTAINER_NAME="stmark-db";

source /opt/project/dev_scripts/docker/ensure-ssh-keys-working.sh;

mkdir -p /opt/project/docker/localStorage;

[[ -e /opt/project/docker/localStorage/${SQL_FILE_NAME} ]] && rm /opt/project/docker/localStorage/${SQL_FILE_NAME};

# Dump the database on remote
ssh -i ~/.ssh/id_rsa -o StrictHostKeyChecking=no -T ${SERVER_USER}@${SERVER_ADDRESS} << HERE
    # Make sure dump file does not exist on host
    [ -e ${REMOTE_SQL_PATH} ] && rm ${REMOTE_SQL_PATH};

    # Make sure dump file does not exist in container
    docker exec --user root --workdir /tmp ${DB_CONTAINER_NAME} bash -c '[ -e ${SQL_FILE_NAME} ] && rm ${SQL_FILE_NAME}';

    # Dump database in Docker container
    docker exec --user root --workdir /tmp ${DB_CONTAINER_NAME} bash -c 'mysqldump -u${DB_USER} -p${PROD_DB_PASSWORD} ${DB_NAME} > ${SQL_FILE_NAME}';

    # Copy dump out of container
    docker cp ${DB_CONTAINER_NAME}:/tmp/${SQL_FILE_NAME} ${REMOTE_SQL_PATH};

    # Delete the dump from the container
    docker exec --user root --workdir /tmp ${DB_CONTAINER_NAME} bash -c '[ -e ${SQL_FILE_NAME} ] && rm ${SQL_FILE_NAME}';
HERE

sleep 5;

# Download database pull
[[ -e "/opt/project/docker/localStorage/${SQL_FILE_NAME}" ]] && rm "/opt/project/docker/localStorage/${SQL_FILE_NAME}";
scp ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_SQL_PATH} /opt/project/docker/localStorage/${SQL_FILE_NAME};

sleep 5;

# Delete database pull on remote
ssh -T ${SERVER_USER}@${SERVER_ADDRESS} rm ${REMOTE_SQL_PATH};

sleep 5;
