FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar tu aplicación al contenedor
COPY ./src /var/www/html

# Ajusta permisos si es necesario
RUN chown -R www-data:www-data /var/www/html
