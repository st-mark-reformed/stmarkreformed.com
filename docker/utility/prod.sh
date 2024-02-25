#!/usr/bin/env bash

while true; do
    chmod -R 0777 /cp-resources-volume;
    chmod -R 0777 /files-volume;
    chmod -R 0777 /files-above-webroot-volume;
    chmod -R 0777 /image-cache-volume;
    chmod -R 0777 /public-cache-volume;
    chmod -R 0777 /storage-volume;
    chmod -R 0777 /uploads-volume;
    sleep 120;
done
