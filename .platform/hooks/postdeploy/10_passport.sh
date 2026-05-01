#!/bin/bash

cd /var/app/current || exit

echo "🔑 Checking Passport keys..."

# Générer les clés si absentes
if [ ! -f storage/oauth-private.key ]; then
    echo "⚠️ Generating Passport keys..."
    php artisan passport:keys
else
    echo "✅ Passport keys already exist"
fi

# Vérifier si client existe
CLIENT_COUNT=$(php artisan tinker --execute="echo \Laravel\Passport\Client::where('personal_access_client', 1)->count();")

if [ "$CLIENT_COUNT" -eq "0" ]; then
    echo "⚠️ Creating personal access client..."
    php artisan passport:client --personal --name="Default Personal Access Client"
else
    echo "✅ Personal access client already exists"
fi

echo "✅ Passport setup complete"