FROM php:8.2-apache as final

# Instala dependências do sistema + Composer + extensões do PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    unzip \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Define o usuário padrão
USER www-data