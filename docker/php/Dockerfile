FROM php:7.4-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
USER www-data

COPY --chown=www-data:www-data composer.json composer.lock symfony.lock ./

ENV APP_ENV=prod

RUN composer install --no-scripts

COPY --chown=www-data:www-data ./ ./

RUN composer dump-autoload && composer run-script post-install-cmd

EXPOSE 8000

CMD ["php", "-S=0.0.0.0:8000", "-t=public/"]