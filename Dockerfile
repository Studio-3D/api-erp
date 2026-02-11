FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Activer mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN cp .env.example .env || true
RUN php artisan key:generate || true

EXPOSE 80

CMD ["apache2-foreground"]
