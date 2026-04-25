FROM php:8.5-fpm

ENV TZ=Asia/Tokyo

RUN apt-get update && \
	apt-get install -y git unzip libzip-dev libicu-dev libonig-dev && \
	docker-php-ext-install intl pdo_mysql pdo_sqlite zip bcmath

RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - && \
	apt-get install -y nodejs && \
	npm install npm@latest -g

COPY ./php.ini /usr/local/etc/php/php.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

