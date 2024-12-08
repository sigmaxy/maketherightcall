FROM php:8.1-apache
COPY site.conf /etc/apache2/sites-enabled/site.conf
RUN apt-get update
RUN a2enmod rewrite
#opcache
RUN docker-php-ext-install opcache
#gd
RUN apt-get install -y \
libfreetype-dev \
libjpeg62-turbo-dev \
libpng-dev \
libzip-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) gd
#data base
RUN docker-php-ext-install mysqli pdo pdo_mysql zip
EXPOSE 80