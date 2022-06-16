FROM php:8.1-fpm-alpine
LABEL maintainer="crea7dos3tos <crea7dos3tos@gmail.com>"
SHELL ["/bin/ash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Tokyo \
  # locale
  LANG=ja_JP.UTF-8 \
  LANGUAGE=ja_JP:ja \
  LC_ALL=ja_JP.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apk upgrade --update && \
  apk --no-cache --update add autoconf gcc g++ make icu-dev libzip-dev tzdata oniguruma-dev imagemagick imagemagick-dev libpng libpng-dev libjpeg-turbo-dev freetype-dev && \
  pecl install imagick && \
  docker-php-ext-enable imagick && \
  docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
  docker-php-ext-install -j$(nproc) gd && \
  cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
  echo ${TZ} > /etc/timezone && \
  docker-php-ext-install intl pdo_mysql mbstring zip bcmath exif && \
  composer config -g repos.packagist composer https://packagist.jp

# JS
RUN apk --no-cache --update add nodejs npm yarn