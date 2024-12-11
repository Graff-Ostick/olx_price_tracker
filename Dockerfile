FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
RUN composer install

EXPOSE 80

CMD ["apache2-foreground"]