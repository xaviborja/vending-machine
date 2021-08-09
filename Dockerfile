FROM php:7.4-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY . /usr/src/app
WORKDIR /usr/src/app

RUN apk update

RUN echo "memory_limit=1024M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN curl --silent --show-error https://getcomposer.org/installer | php && \
    php composer.phar install --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative  --no-interaction && \
    php composer.phar clear-cache && \
    rm -rf /usr/src/php

CMD ["php", "./index.php", "app:initialize-vending-machine"]
