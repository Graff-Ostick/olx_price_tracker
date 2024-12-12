FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    zip \
    unzip \
    curl \
    git \
    libcurl4-openssl-dev \
    default-mysql-client \
    msmtp \
    gettext-base \
    && docker-php-ext-install pdo pdo_mysql curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2enmod rewrite

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

COPY msmtprc.template /etc/msmtprc.template

COPY generate-msmtp-config.sh /usr/local/bin/generate-msmtp-config.sh
RUN chmod +x /usr/local/bin/generate-msmtp-config.sh
RUN touch /etc/msmtprc
RUN chmod +x /etc/msmtprc

RUN echo "sendmail_path = \"/usr/bin/msmtp -t -i\"" >> /usr/local/etc/php/conf.d/sendmail.ini

ENTRYPOINT ["/bin/bash", "-c", "/usr/local/bin/generate-msmtp-config.sh && exec \"$@\"", "--"]
CMD ["apache2-foreground"]

EXPOSE 81
