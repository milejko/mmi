# syntax=docker/dockerfile:1.4

FROM ubuntu:22.04

RUN apt-get update && apt-get upgrade -y && export DEBIAN_FRONTEND=noninteractive && apt-get install -yq \
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