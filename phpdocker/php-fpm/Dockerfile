FROM phpdockerio/php:8.3-fpm
WORKDIR "/workdir"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.3-xdebug php8.3-intl\
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

