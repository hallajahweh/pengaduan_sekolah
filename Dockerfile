FROM php:8.2-apache

# Set document root ke project
WORKDIR /var/www/html

COPY . /var/www/html/

# Install mysqli
RUN docker-php-ext-install mysqli

# Aktifkan rewrite
RUN a2enmod rewrite

# Fix permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80