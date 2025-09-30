FROM php:8.2-cli AS develop

ARG uid
ARG user=developer

ENV APP_HOME=/var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl libpng-dev libonig-dev libxml2-dev libpq-dev \
    zip unzip zlib1g-dev libmcrypt-dev libzip-dev libsodium-dev &&\
    docker-php-ext-install pdo pdo_pgsql pgsql mbstring xml exif pcntl bcmath gd zip sodium && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.5.4 /usr/bin/composer /usr/local/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/developer developer && \
    mkdir -p /home/developer/.composer && \
    chown -R developer:developer /home/developer

ENV PATH="$PATH:/home/developer/.composer/vendor/bin"

WORKDIR $APP_HOME
USER $user
