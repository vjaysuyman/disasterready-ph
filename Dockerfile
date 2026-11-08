# Gamita ang PHP 8.2 nga adunay Apache web server
FROM php:8.2-apache

# I-enable ang PDO MySQL ug cURL extensions (Para sa Database ug Weather API)
RUN docker-php-ext-install pdo pdo_mysql curl

# I-copy ang tanan nimong code ngadto sa web server directory
COPY . /var/www/html/

# I-set ang working directory
WORKDIR /var/www/html