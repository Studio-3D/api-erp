#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1 || php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');" > /dev/null 2>&1; do
    echo "Database not ready, waiting..."
    sleep 2
done
echo "Database connected!"

# Run migrations
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration failed, continuing..."
fi

# Install Passport (only first time)
if [ "${INSTALL_PASSPORT}" = "true" ]; then
    echo "Installing Passport..."
    php artisan passport:install --force || echo "Passport already installed"
fi

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache || true

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Laravel application ready!"

# Execute the main command
exec "$@"
