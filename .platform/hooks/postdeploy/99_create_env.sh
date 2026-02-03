#!/bin/bash

ENV_PATH="/var/app/current/.env"

echo "Creating clean .env file for Laravel..."

rm -f $ENV_PATH
touch $ENV_PATH

printenv | grep -E '^(APP_|DB_|CACHE_|QUEUE_|SESSION_)' | while read line; do
  key=$(echo "$line" | cut -d= -f1)
  value=$(echo "$line" | cut -d= -f2-)
  echo "$key=\"$value\"" >> $ENV_PATH
done

chown webapp:webapp $ENV_PATH
chmod 640 $ENV_PATH

echo ".env generated successfully"
