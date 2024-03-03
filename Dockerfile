FROM php:8.3.3-fpm

# Arguments
ARG user=trzworld
ARG uid=1000

RUN apt-get update && apt-get install -y \
        libicu-dev \
    && docker-php-ext-install \
        intl \
        opcache \
    && docker-php-ext-enable \
        intl \
        opcache \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mysqli

RUN apt-get install -y zip unzip zlib1g-dev libpng-dev libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php

RUN mv composer.phar /usr/local/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html

USER $user
