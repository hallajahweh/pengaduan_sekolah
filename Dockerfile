FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

RUN a2enmod rewrite

RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>" > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom.conf

EXPOSE 80