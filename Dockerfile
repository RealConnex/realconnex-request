FROM php:7.2.7-fpm

RUN apt update && apt install git zip unzip -y

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin/ --filename=composer && \
    rm composer-setup.php


WORKDIR /package