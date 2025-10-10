FROM php:8.2-apache as final

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite && a2enmod actions

# Set permissions for www-data
RUN chown -R www-data:www-data /var/www

USER www-data