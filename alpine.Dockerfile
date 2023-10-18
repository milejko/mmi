ARG ALPINE_VERSION=3.18

FROM alpine:${ALPINE_VERSION}

ARG PHP_VERSION=8.2

#Dirty trick (to fix broken iconv)
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.12/community/ --allow-untrusted gnu-libiconv=1.15-r2
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

RUN export PHP_ALPINE_VERSION=${PHP_VERSION//./} && apk --no-cache add \
	php${PHP_ALPINE_VERSION}-cli \
    \
	php${PHP_ALPINE_VERSION}-bcmath \
    php${PHP_ALPINE_VERSION}-dom \
    php${PHP_ALPINE_VERSION}-fileinfo \
    php${PHP_ALPINE_VERSION}-gd \
    php${PHP_ALPINE_VERSION}-iconv \
    php${PHP_ALPINE_VERSION}-intl \
    php${PHP_ALPINE_VERSION}-ldap \
    php${PHP_ALPINE_VERSION}-mbstring \
    php${PHP_ALPINE_VERSION}-openssl \
    php${PHP_ALPINE_VERSION}-phar \
    php${PHP_ALPINE_VERSION}-pdo \
    php${PHP_ALPINE_VERSION}-pdo_sqlite \
    php${PHP_ALPINE_VERSION}-session \
    php${PHP_ALPINE_VERSION}-simplexml \
    php${PHP_ALPINE_VERSION}-tokenizer \
    php${PHP_ALPINE_VERSION}-xml \
    php${PHP_ALPINE_VERSION}-xmlwriter && \
    if [ -f /usr/bin/php82 ]; then ln -s /usr/bin/php82 /usr/bin/php; fi

#Composer depends on composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
   php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
   php composer-setup.php && \
   php -r "unlink('composer-setup.php');" && \
   mv composer.phar /usr/bin/composer

COPY --link . /app

WORKDIR /app