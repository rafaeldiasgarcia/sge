#
# Define a imagem base para o contêiner da aplicação (PHP 8.2 com Apache).
# Instala as extensões PHP necessárias:
# - pdo, pdo_mysql: para conexão com o banco de dados.
# - intl: para formatação internacional de datas e moedas.
# Habilita o mod_rewrite do Apache para URLs amigáveis.
#
FROM php:8.2-apache as final

# Instala as dependências do sistema para a extensão intl e a própria extensão
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Instala as extensões do banco de dados e habilita o mod_rewrite
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite

USER www-data