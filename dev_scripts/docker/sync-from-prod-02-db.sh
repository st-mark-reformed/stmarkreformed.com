#!/usr/bin/env bash

SQL_FILE_NAME="stmark_prod.sql";

# Clear local database in prep for new dump
mysqldump -u"${DB_USER}" -p"${DB_PASSWORD}" --add-drop-table --no-data site | grep ^DROP | mysql --init-command="SET SESSION FOREIGN_KEY_CHECKS=0;" -u"${DB_USER}" -p"${DB_PASSWORD}" "${DB_DATABASE}" --force;

# Import database dump into local database
mysql -u"${DB_USER}" -p"${DB_PASSWORD}" "${DB_DATABASE}" < "/opt/project/docker/localStorage/${SQL_FILE_NAME}";
