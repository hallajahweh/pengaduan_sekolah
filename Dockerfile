FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

# aktifkan rewrite
RUN a2enmod rewrite

# 🔥 FIX MPM CONFLICT (INI KUNCI ERROR KAMU)
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork

# permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]