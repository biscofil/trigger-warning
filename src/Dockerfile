# Set the base image for subsequent instructions
FROM php:7.4

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

#for php 7.4
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
RUN pecl install mcrypt-1.0.3 && docker-php-ext-enable mcrypt

# Install needed extensions
# Here you can install any other extension that you need during the test and deployment process
RUN docker-php-ext-install pdo_mysql zip

RUN pecl install xdebug

RUN docker-php-ext-enable xdebug

# Install Composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ##########################
# ########################## NPM
# ##########################

RUN apt-get update &&\
    apt-get -y install curl &&\
    curl -sL https://deb.nodesource.com/setup_10.x | bash - &&\
    apt-get -y install nodejs &&\
    ln -s /usr/bin/nodejs /usr/local/bin/node

RUN node -v
RUN npm -v

