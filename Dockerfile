FROM php:8.2-apache as final

# Install Composer and sudo
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y sudo git && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite && a2enmod actions

# Set permissions for www-data
RUN chown -R www-data:www-data /var/www

# Allow Composer to run as root (for Codespaces)
ENV COMPOSER_ALLOW_SUPERUSER=1