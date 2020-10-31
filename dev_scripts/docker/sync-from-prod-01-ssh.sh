#!/usr/bin/env bash

SERVER_USER="buzzingpixel";
SERVER_ADDRESS="67.205.164.226";
SQL_FILE_NAME="stmark_prod.sql";
REMOTE_PROJECT_PATH="/var/www/stmarkreformed.com";
REMOTE_SQL_PATH="${REMOTE_PROJECT_PATH}/${SQL_FILE_NAME}";
DB_NAME="stmarkreformed_com";
DB_USER="stmarkreformed_com";

source /opt/project/dev_scripts/docker/ensure-ssh-keys-working.sh;

mkdir -p /opt/project/docker/localStorage;

[[ -e /opt/project/docker/localStorage/${SQL_FILE_NAME} ]] && rm /opt/project/docker/localStorage/${SQL_FILE_NAME};

# Dump the database on remote
ssh -i ~/.ssh/id_rsa -o StrictHostKeyChecking=no -T ${SERVER_USER}@${SERVER_ADDRESS} << HERE
    # Make sure dump file does not exist on host
    [ -e ${REMOTE_SQL_PATH} ] && rm ${REMOTE_SQL_PATH};

    mysqldump -u${DB_USER} -p${PROD_DB_PASSWORD} ${DB_NAME} > ${REMOTE_SQL_PATH};
HERE

sleep 5;

# Download database pull
[[ -e "/opt/project/docker/localStorage/${SQL_FILE_NAME}" ]] && rm "/opt/project/docker/localStorage/${SQL_FILE_NAME}";
scp ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_SQL_PATH} /opt/project/docker/localStorage/${SQL_FILE_NAME};

sleep 5;

# Delete database pull on remote
ssh -T ${SERVER_USER}@${SERVER_ADDRESS} rm ${REMOTE_SQL_PATH};

sleep 5;
