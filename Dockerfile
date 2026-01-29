FROM php:8.2-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilita mod_rewrite de Apache
RUN a2enmod rewrite


