FROM php:8.1-apache
RUN a2enmod rewrite
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html/logs
EXPOSE 80
