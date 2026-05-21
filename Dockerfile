FROM wordpress:6.4-php8.2-apache

COPY wp-content/ /var/www/html/wp-content/

RUN chown -R www-data:www-data /var/www/html/wp-content

EXPOSE 80