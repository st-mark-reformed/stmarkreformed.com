#!/usr/bin/env bash

source /opt/project/scripts/dev/ensure-ssh-keys-working.sh;

rsync -e "ssh -o StrictHostKeyChecking=no" -av buzzingpixel@67.205.164.226:/var/www/stmarkreformed.com/storage/public/uploads/ /opt/project/public/uploads --delete
