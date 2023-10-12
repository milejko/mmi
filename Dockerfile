# syntax=docker/dockerfile:1.4

FROM debian:12-slim

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    php-cli \
    php-ldap \
    php-mysql \
    php-pdo \
    php-pdo-mysql \
    php-pdo-sqlite \
    php-dom \
    php-bcmath \
    php-xdebug \
    php-curl \
    php-gd \
    php-zip \
    composer

COPY --link . /app

WORKDIR /app