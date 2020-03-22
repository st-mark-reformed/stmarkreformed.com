#!/usr/bin/env bash

mysqldump -u${DB_USER} -p${DB_PASSWORD} --add-drop-table --no-data ${DB_DATABASE} | grep ^DROP | mysql --init-command="SET SESSION FOREIGN_KEY_CHECKS=0;" -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE} --force;

gunzip < /opt/project/localStorage/db_pull.sql.gz | mysql -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE};
