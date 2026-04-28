#!/bin/bash
set -e

cd /var/www/html

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force
