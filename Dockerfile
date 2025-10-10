FROM php:8.2-apache as final

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite && a2enmod actions

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1