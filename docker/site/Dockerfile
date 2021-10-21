# ---------------------------------------------------------
#RUN composer install --no-dev --no-progress --no-scripts --prefer-dist --no-cache --ignore-platform-reqs --optimize-autoloader
# ---------------------------------------------------------

FROM php:8.0-apache

#ENV APP_ENV prod
#ENV APP_DEBUG false
ENV APP_ENV dev
ENV APP_DEBUG true

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		mc wget unzip gnupg git \
	&& wget https://get.symfony.com/cli/installer -O - | bash \
	&& mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# nvm + node.js
RUN wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && npm install --global yarn \
    && yarn install \
    && pecl install apcu

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip intl xdebug opcache

RUN install-php-extensions mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

COPY ./php.ini /usr/local/etc/php/conf.d/docker-php.ini

RUN a2enmod rewrite && service apache2 restart

USER root
