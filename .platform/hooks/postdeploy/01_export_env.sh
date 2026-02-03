#!/bin/bash

echo "Exporting EB environment variables to PHP-FPM..."

ENV_FILE="/etc/php-fpm.d/www.conf"

# Nettoyage des anciennes lignes
sed -i '/^env\[/d' $ENV_FILE

# Export de toutes les variables EB utiles
printenv | grep -E '^(APP_|DB_|AWS_)' | while read line; do
  key=$(echo $line | cut -d= -f1)
  value=$(echo $line | cut -d= -f2-)
  echo "env[$key] = $value" >> $ENV_FILE
done

# Redémarrage PHP-FPM
systemctl restart php-fpm
