FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl libsqlite3-dev \
    && docker-php-ext-install zip pdo pdo_sqlite \
    && a2enmod rewrite

COPY . /var/www/html
COPY .env.example /var/www/html/.env

RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage/framework/sessions \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod 664 /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite

RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

RUN php artisan key:generate

EXPOSE 80
