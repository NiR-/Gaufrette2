FROM php:7-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mbstring zip \
    && pecl install xdebug-beta \
    && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" >> /usr/local/etc/php/php.ini \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /src/
COPY composer.* /src/
RUN composer install
ENV PATH /src/vendor/bin:$PATH

COPY ./ /src/
VOLUME ["/src"]

