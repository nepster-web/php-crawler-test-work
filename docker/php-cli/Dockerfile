# https://hub.docker.com/_/php/
# PHP7-CLI
FROM php:7.2.5-cli

WORKDIR /www


RUN apt-get update -qq \
    && apt-get install -qy --no-install-recommends \
        git \
        openssl \
        librecode0 \
        uuid-dev \
        libmagickwand-dev \
        libsasl2-dev \
        libxml2 \
        libxslt-dev


RUN apt-get update -qq \
    && apt-get install -qy --no-install-recommends \
        autoconf \
        file \
        g++ \
        gcc \
        libc-dev \
        make \
        cmake \
        curl \
        pkg-config \
        libtool \
        tar \
        libmcrypt-dev \
        libpng-dev \
        zip \
        unzip \
        wget


RUN mkdir /var/log/php


RUN docker-php-ext-install \
        gd \
        mbstring \
        zip \
        xsl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc && \
        composer