#!/usr/bin/env bash

SERVER_USER="buzzingpixel";
SERVER_ADDRESS="67.205.164.226";

REMOTE_FILES_DIR="/var/www/stmarkreformed.com/storage/public/files";
LOCAL_FILES_DIR="/opt/project/public/files";
REMOTE_UPLOADS_DIR="/var/www/stmarkreformed.com/storage/public/uploads";
LOCAL_UPLOADS_DIR="/opt/project/public/uploads";

source /opt/project/dev_scripts/docker/ensure-ssh-keys-working.sh;

# Rsync directories

rsync -e "ssh -o StrictHostKeyChecking=no" -av ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_FILES_DIR}/ ${LOCAL_FILES_DIR} --delete;

rsync -e "ssh -o StrictHostKeyChecking=no" -av ${SERVER_USER}@${SERVER_ADDRESS}:${REMOTE_UPLOADS_DIR}/ ${LOCAL_UPLOADS_DIR} --delete;
