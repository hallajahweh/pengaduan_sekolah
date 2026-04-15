FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

# aktifkan rewrite (penting untuk MVC)
RUN a2enmod rewrite

# allow .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# set folder permission aman
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]