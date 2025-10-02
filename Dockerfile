#
# Dockerfile para o contêiner da aplicação PHP com Apache
# Inclui extensões pdo, pdo_mysql e intl, habilita o mod_rewrite
# e instala o Composer globalmente.
#

FROM php:8.2-apache as final

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Instala o Composer copiando da imagem oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o usuário padrão como www-data
USER www-data
