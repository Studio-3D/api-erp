FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl

RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

RUN chown -R www-data:www-data /var/www

EXPOSE 80

CMD php-fpm -D && nginx -g "daemon off;"
