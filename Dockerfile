FROM php:8.2.24-alpine3.20

LABEL maintainer="Gustavo V. Goulart <gvillela7@gmail.com>"

WORKDIR /var/www/html/oltrust

# Install dependencies
RUN apk add --no-cache --update linux-headers \
        bash \
        $PHPIZE_DEPS \
        unzip \
        pkgconfig \
        icu-dev \
        curl-dev \
        openssl-dev \
        libxml2-dev \
        libzip-dev \
        oniguruma-dev \
        readline-dev \
        libxslt-dev \
        libmemcached-dev \
        libpng-dev \
        freetype-dev \
        libjpeg-turbo-dev

RUN pecl install memcached redis protobuf mongodb excimer \
    && docker-php-ext-enable memcached opcache redis protobuf mongodb \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install -j "$(nproc)" sockets mysqli pdo_mysql intl zip xsl soap gd \
    && docker-php-source delete \
    && apk del --no-cache \
        gcc \
        g++ \
        make \
        perl \
        autoconf \
        dpkg-dev \
        dpkg \
        file \
        libc-dev \
        pkgconf \
        re2c

# Configure OpenTelemetry extension
#RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
#      opentelemetry

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --chown=www-data:www-data ./ /var/www/html/oltrust
COPY --chown=www-data:www-data ./.env /var/www/html/oltrust/.env
COPY --chown=www-data:www-data ./docker/php.ini /usr/local/etc/php/php.ini

#RUN chown -R www-data:www-data /var/www/html/oltrust
RUN cd /var/www/html/oltrust && composer install --no-dev && php artisan config:cache && composer dump-autoload

EXPOSE 8000
ENTRYPOINT ["./docker/entrypoint.sh"]
