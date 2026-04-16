FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

# aktifkan apache rewrite
RUN a2enmod rewrite

# set permission aman
RUN chown -R www-data:www-data /var/www/html

# pastikan apache jalan stabil
EXPOSE 80

CMD ["apache2-foreground"]