FROM registry.gitlab.ulb.be/openshift-resources/base-images/api-platform/composer-2:latest AS composer
FROM php:8.2-fpm AS base

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    zip \
    npm \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql intl zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /srv/app

# Copy application directory contents
COPY . .

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

# Run Composer & NPM
COPY --from=composer /composer /usr/bin/composer

RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache; \
	composer dump-autoload --classmap-authoritative --no-dev;

RUN npm i vite && npm run build

# Expose port 9000 and start php-fpm server
# EXPOSE 9000
CMD ["php-fpm"]