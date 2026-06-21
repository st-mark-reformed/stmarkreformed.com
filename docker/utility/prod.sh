#!/usr/bin/env bash

while true; do
    chmod -R 0777 /files-volume;
    chmod -R 0777 /files-above-webroot-volume;
    chmod -R 0777 /uploads-volume;
    sleep 120;
done
