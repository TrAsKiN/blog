FROM php:8.1-apache

COPY docker/apache2.conf ${APACHE_CONFDIR}/apache2.conf
COPY docker/security.conf ${APACHE_CONFDIR}/conf-available/security.conf
COPY docker/blog.conf ${APACHE_CONFDIR}/sites-available/blog.conf

RUN apt-get update && apt-get install -y libicu-dev && \
    docker-php-ext-install pdo pdo_mysql intl && \
    docker-php-ext-enable opcache && \
    a2enmod rewrite && \
    a2dissite 000-default && \
    a2ensite blog && \
    service apache2 restart

WORKDIR /var/www

EXPOSE 80 443
