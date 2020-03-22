#!/usr/bin/env bash

source /opt/project/scripts/dev/ensure-ssh-keys-working.sh;

[[ -e /opt/project/localStorage/db_pull.sql.gz ]] && rm /opt/project/localStorage/db_pull.sql.gz;

ssh -i ~/.ssh/id_rsa -oStrictHostKeyChecking=no buzzingpixel@67.205.164.226 mysqldump -ustmarkreformed_com -p${PROD_DB_PASSWORD} stmarkreformed_com | gzip -9 > /opt/project/localStorage/db_pull.sql.gz;

#ssh -i ~/.ssh/id_rsa -o StrictHostKeyChecking=no -T buzzingpixel@67.205.164.226 << HERE
#    # Make sure dump file does not exist on host
#    [ -e /var/www/stmarkreformed.com/db_pull.sql ] && rm /var/www/stmarkreformed.com/db_pull.sql;
#HERE
