#!/usr/bin/env bash

# Reset
Reset="\033[0m"; # Text Reset

# Regular Colors
Black="\033[0;30m"; # Black
Red="\033[0;31m"; # Red
Green="\033[0;32m"; # Green
Yellow="\033[0;33m"; # Yellow
Blue="\033[0;34m"; # Blue
Purple="\033[0;35m"; # Purple
Cyan="\033[0;36m"; # Cyan
White="\033[0;37m"; # White

# Bold
BBlack="\033[1;30m"; # Black
BRed="\033[1;31m"; # Red
BGreen="\033[1;32m"; # Green
BYellow="\033[1;33m"; # Yellow
BBlue="\033[1;34m"; # Blue
BPurple="\033[1;35m"; # Purple
BCyan="\033[1;36m"; # Cyan
BWhite="\033[1;37m"; # White

if [[ "${1}" = "staging" ]]; then
    PASSWORD=$(grep STAGING_DB_PASSWORD /app/.env | xargs)
    IFS='=' read -ra PASSWORD <<< "${PASSWORD}"
    DB_USER="stagingstmarkreformed"
    DB_DATABASE="stagingstmarkreformed"
    DB_PASSWORD=${PASSWORD[1]}
    REMOTE_USER="buzzingpixel"
    REMOTE_HOST="165.227.207.4"
    REMOTE_UPLOADS_DIRECTORY="/var/www/stagingstmarkreformed.buzzingpixel.com/storage/public/uploads"
elif [[ "${1}" = "prod" ]]; then
    printf "${BRed}PROD NOT YET SET UP${Reset}";
    exit 1;
else
    printf "${BRed}Unrecognized environment${Reset}";
    exit 1;
fi

if [[ -z "${DB_PASSWORD}" ]]; then
    printf "${BRed}Password not set in .env${Reset}";
    exit 1;
fi

mkdir -p ~/.ssh;
cp /tmp/.ssh/id_rsa ~/.ssh/id_rsa;
chmod 0600 ~/.ssh/id_rsa;

printf "${Yellow}Getting ${1} database...${Reset}";
echo "";
ssh -oStrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} mysqldump -u${DB_USER} -p${DB_PASSWORD} ${DB_DATABASE} | gzip -9 > /app/localStorage/${DB_DATABASE}.sql.gz;

printf "${Yellow}Clearing local database...${Reset}";
echo "";
mysqldump -usite -psecret --add-drop-table --no-data site | grep ^DROP | mysql --init-command="SET SESSION FOREIGN_KEY_CHECKS=0;" -usite -psecret site --force;

printf "${Yellow}Importing ${1} database...${Reset}";
echo "";
gunzip < /app/localStorage/${DB_DATABASE}.sql.gz | mysql -usite -psecret site;

printf "${Yellow}Syncing uploads directory from ${1}...${Reset}";
echo "";
rsync -e "ssh -o StrictHostKeyChecking=no" -av ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_UPLOADS_DIRECTORY}/ /app/public/uploads --delete

printf "${Green}Sync complete${Reset}";
echo "";
