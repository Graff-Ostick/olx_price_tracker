FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    zip \
    unzip \
    curl \
    git \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

RUN sleep 10 && php /var/www/html/sql/install.php

EXPOSE 81

CMD ["apache2-foreground"]