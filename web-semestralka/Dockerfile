FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql
# 1) systémové balíčky
RUN apt-get update && apt-get install -y git unzip \
    && rm -rf /var/lib/apt/lists/*

# 2) composer z oficiálního image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html