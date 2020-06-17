FROM 10.49.0.1:5000/php74laravel

COPY  * /var/www/html/
COPY  .htaccess /var/www/html/
COPY  .env      /var/www/html/
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 777 /var/www/html

