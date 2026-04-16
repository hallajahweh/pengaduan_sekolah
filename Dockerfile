FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

# aktifkan rewrite untuk MVC routing
RUN a2enmod rewrite

# izinkan .htaccess jalan
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]