FROM php:8.2-apache

WORKDIR /var/www/html

# Enable Apache rewrite support and copy the application.
RUN a2enmod rewrite
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
COPY . /var/www/html/

# Apache must listen on Railway's assigned PORT.
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R ug+rwX /var/www/html

EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
