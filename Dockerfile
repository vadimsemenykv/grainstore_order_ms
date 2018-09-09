FROM webdevops/php-nginx:7.2

COPY . ./app/
WORKDIR /app/

ENV WEB_DOCUMENT_ROOT="/app/public"

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install PHP dependencies
RUN composer --no-progress --prefer-dist install