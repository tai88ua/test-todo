FROM php:8.1-fpm

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#install other
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql

RUN apt-get -y update
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y libzip-dev

RUN docker-php-ext-install zip


#set work dir
WORKDIR /usr/share/nginx/html
