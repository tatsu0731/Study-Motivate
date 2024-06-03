FROM devilbox/php-fpm-8.1:latest
COPY php.ini /usr/local/etc/php/

ADD setup/xdebug.ini /tmp/xdebug.ini

RUN apt-get update \
  && apt-get install -y libzip-dev zlib1g-dev unzip libpq-dev \
  && docker-php-ext-install zip

# nodejs install
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
  && apt-get install -y nodejs \
  && apt-get install -y vim \
  && docker-php-ext-install sockets pdo_pgsql \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug

# Composer2.0 install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update --2
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

WORKDIR /work/web
