#!/usr/bin/env bash

chmod -R 0666 /opt/project/system/user/config/config.php;

mkdir -p /log-volume/nginx;

touch /log-volume/nginx/error.log;

chmod -R 0777 /log-volume;

while true; do
    chmod -R 0777 /image-cache-volume;
    chmod -R 0777 /files-volume;
    chmod -R 0777 /public-cache-volume;
    chmod -R 0777 /storage-volume;
    chmod -R 0777 /uploads-volume;
    sleep 120;
done
