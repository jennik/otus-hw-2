FROM php:7.4-fpm-alpine

RUN apk update && \
    apk add postgresql-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apk del .build-deps

#RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && pecl install xdebug \
#    && docker-php-ext-enable xdebug \
#    && apk del .build-deps
#COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
USER www-data

COPY --chown=www-data:www-data composer.json composer.lock symfony.lock ./

ENV APP_ENV=prod

RUN composer install --no-scripts

COPY --chown=www-data:www-data ./ ./

RUN composer dump-autoload && composer run-script post-install-cmd

EXPOSE 8000

CMD ["php", "-S=0.0.0.0:8000", "-d max_execution_time=1", "-t=public/"]