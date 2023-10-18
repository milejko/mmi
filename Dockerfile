ARG DEBIAN_VERSION=12
ARG DEBIAN_VARIANT=-slim

FROM debian:${DEBIAN_VERSION}${DEBIAN_VARIANT}

ARG PHP_VERSION=8.2

RUN apt update && \
    apt install -yq curl lsb-release && \
    curl https://packages.sury.org/php/apt.gpg -o /usr/share/keyrings/deb.sury.org-php.gpg && \
	echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php-sury.list && \
	apt update && apt install -yq \
	php${PHP_VERSION}-cli \
	php${PHP_VERSION}-fpm \
	php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-pdo-sqlite \
    php${PHP_VERSION}-mbstring \
	#php${PHP_VERSION}-curl \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-ldap \
    #php${PHP_VERSION}-simplexml \
	composer

COPY --link . /app

WORKDIR /app