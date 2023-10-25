ARG PHP_VERSION=8.2

FROM milejko/php:${PHP_VERSION}-cli

RUN apt update && apt install -yq \
    php${PHP_VERSION}-pdo-sqlite \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-ldap \
    php${PHP_VERSION}-xdebug

COPY --link . /app

WORKDIR /app