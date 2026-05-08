ARG PHP_VERSION=8.5

FROM milejko/php:${PHP_VERSION}-cli

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage

COPY --link . .

RUN composer install
