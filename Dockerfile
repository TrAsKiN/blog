FROM php:8.1-apache

RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!ServerTokens OS!ServerTokens Prod!g' /etc/apache2/conf-available/security.conf \
    && sed -ri -e 's!ServerSignature On!ServerSignature Off!g' /etc/apache2/conf-available/security.conf \
    && sed -i -e '$ a ServerName 127.0.0.1' /etc/apache2/apache2.conf

RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable opcache \
    && a2enmod rewrite \
    && service apache2 restart

WORKDIR /var/www
