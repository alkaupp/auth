FROM php:7.4-apache
RUN apt-get update \
&& apt-get install -y libpq-dev curl zlib1g-dev libzip-dev \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql zip \
&& a2enmod rewrite
