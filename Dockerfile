FROM php:7.2.7-fpm

RUN apt update && apt install git zip unzip -y

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin/ --filename=composer && \
    rm composer-setup.php

RUN pecl install -o -f xdebug
RUN docker-php-ext-enable xdebug
RUN sed -i 's/9000/9005/' /usr/local/etc/php-fpm.d/zz-docker.conf
RUN printf "xdebug.remote_enable=1\n xdebug.remote_autostart=1\n xdebug.remote_host=172.18.89.217\n xdebug.remote_connect_back=off\n xdebug.remote_port=9000\n xdebug.idekey=PHPSTORM\n xdebug.max_nesting_level=1500" >> "/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini"

WORKDIR /package