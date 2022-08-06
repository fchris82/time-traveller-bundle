FROM php:8.1-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV XDEBUG_MODE=off

RUN apk add --no-cache $PHPIZE_DEPS git  \
    && pecl install xdebug  \
    && docker-php-ext-enable xdebug
