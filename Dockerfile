FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
COPY index.html saveRecord.php success.html schema.sql /var/www/html/
RUN mkdir -p /var/www/html/uploads && chown www-data:www-data /var/www/html/uploads