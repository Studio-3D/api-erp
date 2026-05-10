#!/bin/bash
set -e

cd /var/www

mkdir -p storage

echo "Loading Passport keys from S3..."

aws s3 cp s3://erp-immo-prod-storage/private/passport/oauth-private.key storage/oauth-private.key
aws s3 cp s3://erp-immo-prod-storage/private/passport/oauth-public.key storage/oauth-public.key

chmod 600 storage/oauth-private.key
chmod 644 storage/oauth-public.key

php artisan optimize:clear || true

exec "$@"