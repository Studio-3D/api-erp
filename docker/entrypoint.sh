#!/bin/sh
set -e

# 🔥 REDIRIGE TOUTE LA SORTIE VERS UN FICHIER
exec > /var/log/container.log 2>&1

# 🔥 MODE DEBUG : affiche chaque commande
set -x

cd /var/www

echo "🚀 Initializing Laravel..."

# Copier .env si absent
if [ ! -f .env ]; then
    echo "Creating .env from example"
    cp .env.example .env
fi

# Générer APP_KEY si vide
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Générer clés Passport si absentes
if [ ! -f storage/oauth-private.key ]; then
    echo "Generating Passport keys..."
    php artisan passport:keys --force
fi

# Permissions runtime
chown -R www-data:www-data storage bootstrap/cache

exec /usr/bin/supervisord