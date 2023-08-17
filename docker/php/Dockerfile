# Image and version
FROM php:8.0-fpm

# Install dependencies
RUN apt update && \
    apt install --no-install-recommends -y \
    zlib1g-dev \
    libpng-dev \
    libzip-dev \
    unzip

# Call PHP images script `docker-php-ext-install` and install language extensions
RUN docker-php-ext-install  \
    zip

# copy the Composer PHAR from the Composer image into the PHP image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini