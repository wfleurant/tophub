FROM php:fpm

RUN apt-get update && \
        DEBIAN_FRONTEND=noninteractive apt-get install -y \
        libmcrypt-dev php5-memcached php-pear libssl-dev && \
    apt-get clean

RUN docker-php-ext-install mcrypt

RUN pecl install -f mongo && \
        mkdir -p /etc/php5/mods-available && \
        echo 'extension=mongo.so' > /usr/local/etc/php/conf.d/mongo.ini
