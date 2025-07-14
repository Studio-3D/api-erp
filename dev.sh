#!/bin/bash

# Exit script if any command fails
set -e

echo "Starting backend setup script for DEVELOPMENT..."

# First, update the DB_HOST in the .env file to point to the Docker service name
sed -i 's/DB_HOST=.*/DB_HOST=db/g' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/g' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=Kilo15.35/g' .env
sed -i 's/APP_ENV=.*/APP_ENV=local/g' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=true/g' .env

echo "Modified database configuration to use local Docker database"

# Wait for the MySQL server to be ready
echo "Waiting for MySQL to be ready..."
MAX_RETRIES=30
count=0

while ! php -r "try { \$dbh = new PDO('mysql:host=db;port=3306', 'root', 'Kilo15.35'); echo 'Connected successfully'; } catch(PDOException \$e) { exit(1); }" 2>/dev/null; do
    count=$((count+1))
    if [ $count -gt $MAX_RETRIES ]; then
        echo "Error: MySQL did not become ready in time."
        exit 1
    fi
    echo "MySQL not ready yet... waiting 2 seconds"
    sleep 2
done

echo "MySQL server is now available."

# Run migrations
php artisan migrate --force
echo "Database migrated successfully"

# php artisan db:seed --force
# echo "Database seeded successfully"

php artisan passport:install --force
echo "Passport installed successfully"

# Check if pusher:install command exists (Laravel may not have this command by default)
if php artisan | grep -q "pusher:install"; then
    php artisan pusher:install
    echo "Pusher installed successfully"
else
    echo "Pusher command not found, skipping"
fi

# Clear caches in development
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "Starting Laravel server on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000
