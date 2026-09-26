FROM php:8.3-apache

RUN docker-php-ext-install mysqli pdo_mysql

# Don't serve logs, SQL dumps or the Dockerfile itself
RUN printf '<FilesMatch "\\.(log|sql)$|^Dockerfile$">\n    Require all denied\n</FilesMatch>\n' \
    > /etc/apache2/conf-enabled/deny-private.conf

COPY . /var/www/html/
RUN rm -rf /var/www/html/.git \
    && mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html

# Railway provides $PORT; make Apache listen on it.
# Also ensure only the prefork MPM is enabled (required by mod_php).
CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod -q mpm_prefork >/dev/null && sed -i \"s/Listen 80/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-80}>/\" /etc/apache2/sites-enabled/000-default.conf && apache2-foreground"]
