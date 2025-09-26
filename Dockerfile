#
# Define a imagem base para o contêiner da aplicação (PHP 8.2 com Apache).
# Instala as extensões PHP necessárias (PDO para banco de dados) e habilita o mod_rewrite do Apache
# para permitir URLs amigáveis.
#
FROM php:8.2-apache as final
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite
USER www-data