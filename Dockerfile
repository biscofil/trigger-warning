FROM node:14.15.5 as node

WORKDIR /app

COPY package.json package.json
COPY package-lock.json package-lock.json
COPY resources/js resources/js
COPY resources/sass resources/sass
COPY webpack.mix.js webpack.mix.js

RUN npm install
RUN npm run production

# Set the base image for subsequent instructions
FROM php:7.4-apache

WORKDIR /var/www/html

# Update packages
RUN apt-get update

RUN apt-get install -y --no-install-recommends apt-utils

# Install PHP and composer dependencies
RUN apt-get install -qq git curl libmcrypt-dev libpng-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev zip unzip
# sendmail

#for php 7.3+
RUN apt-get install -qq libzip-dev

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

#for php 7.3
#RUN docker-php-ext-configure gd \
#  --with-gd \
#  --with-jpeg-dir \
#  --with-png-dir \
#  --with-zlib-dir

#for php 7.4+
RUN docker-php-ext-configure gd \
    --with-jpeg=/usr/include/

RUN docker-php-ext-install gd

# for phpunit timeout
RUN docker-php-ext-install pcntl

# Clear out the local repository of retrieved package files
RUN apt-get clean

#for php 7.2
#RUN pecl install mcrypt-1.0.1 && docker-php-ext-enable mcrypt

#for php 7.3
#RUN pecl install mcrypt-1.0.2 && docker-php-ext-enable mcrypt

#for php 7.4
#RUN pecl install mcrypt-1.0.3 && docker-php-ext-enable mcrypt

#for php 8
#RUN pecl install mcrypt && docker-php-ext-enable mcrypt

# Install needed extensions
# Here you can install any other extension that you need during the test and deployment process
RUN docker-php-ext-install pdo_mysql zip

# for php 7.4
RUN pecl install xdebug-3.1.5

# for php 8
# RUN pecl install xdebug

RUN docker-php-ext-enable xdebug

# Install Composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/
RUN composer install

COPY --from=node /app/public /var/www/html/public

# Copy custom Apache site configuration
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache rewrite module
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80