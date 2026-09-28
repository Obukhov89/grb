FROM maximniistrom/bitrix:ic.23.300.101-custom_2

COPY . /var/www/html

# PHP
COPY ./init/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini
COPY ./init/php/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh

# Nginx
COPY ./init/nginx/conf /etc/nginx/
COPY ./init/nginx/nginx-entrypoint.sh /usr/local/bin/nginx-entrypoint.sh

# Устанавливаем dos2unix и конвертируем скрипты в формат LF
RUN apt-get update && apt-get install -y dos2unix && \
    dos2unix /usr/local/bin/nginx-entrypoint.sh /usr/local/bin/app-entrypoint.sh

# Apache
COPY ./init/apache/ports.conf /etc/apache2/ports.conf
RUN sed -i 's/: ${APACHE_RUN_USER:=www-data}/: ${APACHE_RUN_USER:=bitrix}/' /etc/apache2/envvars && \
    sed -i 's/: ${APACHE_RUN_GROUP:=www-data}/: ${APACHE_RUN_GROUP:=bitrix}/' /etc/apache2/envvars

# Xdebug
RUN if ! pecl list | grep -q xdebug; then pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug; fi
ENV PHP_IDE_CONFIG 'serverName=app-dev'
COPY ./init/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Пока так, в дальнейшем избавимся от ./init
RUN rm -rf /var/www/html/init

# Установка владельца и прав для bitrix
RUN chmod +x /usr/local/bin/nginx-entrypoint.sh /usr/local/bin/app-entrypoint.sh \
    && chown -R bitrix:bitrix /etc/nginx /usr/local/bin/nginx-entrypoint.sh /usr/local/bin/app-entrypoint.sh \
    && mkdir -p /var/run /var/cache/nginx \
    && chown -R bitrix:bitrix /var/run /var/cache/nginx;

WORKDIR /var/www/html