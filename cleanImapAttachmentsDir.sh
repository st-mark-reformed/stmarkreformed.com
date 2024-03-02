#!/usr/bin/env bash

echo "Cleaning IMAP attachments directory"

find /var/www/storage/imap-attachments -mmin +10 -type f -delete

