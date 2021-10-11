#!/usr/bin/env bash

SERVER_USER="root";
SERVER_ADDRESS="206.81.13.32";

REMOTE_FILES_DIR="/var/lib/docker/volumes/stmark_files-volume/_data";
LOCAL_FILES_DIR="/opt/project/public/files";
REMOTE_UPLOADS_DIR="/var/lib/docker/volumes/stmark_uploads-volume/_data";
LOCAL_UPLOADS_DIR="/opt/project/public/uploads";

source /opt/project/docker/scripts/ensure-ssh-keys-working.sh;

# Rsync directories

rsync -e "ssh -o StrictHostKeyChecking=no" -av ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_FILES_DIR}/ ${LOCAL_FILES_DIR} --delete;

rsync -e "ssh -o StrictHostKeyChecking=no" -av ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_UPLOADS_DIR}/ ${LOCAL_UPLOADS_DIR} --delete;
