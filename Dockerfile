FROM php:8.2-apache

# 1. I-install ang system dependency para sa cURL
RUN apt-get update && apt-get install -y libcurl4-openssl-dev

# 2. I-install ang PHP extensions (pdo, pdo_mysql, ug curl)
RUN docker-php-ext-install pdo pdo_mysql curl

# 3. Kopyaha ang code
COPY . /var/www/html/

# 4. Set working directory
WORKDIR /var/www/html