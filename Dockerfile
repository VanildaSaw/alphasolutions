FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads /var/www/html/relatorios
RUN chown -R www-data:www-data /var/www/html/uploads /var/www/html/relatorios
RUN chmod -R 775 /var/www/html/uploads /var/www/html/relatorios
EXPOSE 80
