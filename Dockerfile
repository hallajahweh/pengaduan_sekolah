FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

# aktifkan rewrite
RUN a2enmod rewrite

# 🔥 paksa hanya prefork (hindari konflik MPM)
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork || true

# install dependency penting (biar apache stabil)
RUN apt-get update && apt-get install -y \
    libapache2-mod-php \
    && rm -rf /var/lib/apt/lists/*

# permission aman
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]