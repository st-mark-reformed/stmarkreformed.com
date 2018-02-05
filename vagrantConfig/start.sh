#!/bin/sh

# Make sure NGINX restarts after NFS is mounted so it can pick up the NGINX conf
sudo service nginx restart;
