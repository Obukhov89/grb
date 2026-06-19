FROM maximniistrom/bitrix:soex.25.100.400

# Копируем проект
COPY . /var/www/html

# Копируем кастомный php.ini
COPY ./init/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# PHP
COPY ./init/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Nginx
COPY ./init/nginx/conf /etc/nginx/
COPY ./init/nginx/nginx-entrypoint.sh /usr/local/bin/nginx-entrypoint.sh

# Устанавливаем dos2unix и конвертируем скрипт в формат LF
RUN apt-get update && apt-get install -y dos2unix && \
    dos2unix /usr/local/bin/nginx-entrypoint.sh

# Apache
COPY ./init/apache/ports.conf /etc/apache2/ports.conf
RUN sed -i 's/: ${APACHE_RUN_USER:=www-data}/: ${APACHE_RUN_USER:=bitrix}/' /etc/apache2/envvars && \
    sed -i 's/: ${APACHE_RUN_GROUP:=www-data}/: ${APACHE_RUN_GROUP:=bitrix}/' /etc/apache2/envvars

# Удаляем init, если вдруг попал
RUN rm -rf /var/www/html/init

# Установка владельца и прав для bitrix
RUN chmod +x /usr/local/bin/nginx-entrypoint.sh \
    && chown -R bitrix:bitrix /etc/nginx /usr/local/bin/nginx-entrypoint.sh \
    && mkdir -p /var/run /var/cache/nginx \
    && chown -R bitrix:bitrix /var/run /var/cache/nginx \
    && chown -R bitrix:bitrix /var/www/html/laravel \
    && find /var/www/html/laravel -type d -exec chmod 755 {} \; \
    && find /var/www/html/laravel -type f -exec chmod 644 {} \;

WORKDIR /var/www/html

# Запуск composer install для всех нужных директорий
RUN composer install --no-interaction --prefer-dist \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html/laravel \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html/ulab/assets/composer

# Запускаем supervisor, чтобы работали и apache, и cron
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]