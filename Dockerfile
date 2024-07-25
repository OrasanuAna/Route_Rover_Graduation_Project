FROM php:8.1-apache

# Instalează extensii PHP necesare
RUN docker-php-ext-install mysqli

# Copiază fișierele aplicației în directorul de lucru al Apache
COPY . /var/www/html/

# Setează permisiunile pentru directorul Apache
RUN chown -R www-data:www-data /var/www/html

# Expune portul 80 pentru HTTP
EXPOSE 80
