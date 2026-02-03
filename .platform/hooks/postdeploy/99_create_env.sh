#!/bin/bash

ENV_PATH="/var/app/current/.env"

echo "Creating .env file for Laravel..."

rm -f $ENV_PATH
touch $ENV_PATH

printenv | grep -E '^(APP_|DB_|CACHE_|QUEUE_|SESSION_)' >> $ENV_PATH

chown webapp:webapp $ENV_PATH
chmod 640 $ENV_PATH

echo ".env created successfully"
