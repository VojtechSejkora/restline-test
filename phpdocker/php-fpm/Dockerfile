FROM phpdockerio/php:8.1-fpm
WORKDIR "/workdir"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.1-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy php settings
COPY ./php.ini ${PHP_INI_DIR}/conf.d/99-php.ini
