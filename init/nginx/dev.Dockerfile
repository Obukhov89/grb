FROM nginx:1.28-alpine

# Добавляем пользователя и группу bitrix с UID и GID 600
RUN addgroup -g 600 bitrix \
    && adduser -D -u 600 -G bitrix -s /bin/sh bitrix

# Копируем все конфиги и каталоги
COPY ./conf/ /etc/nginx/

# Копируем entrypoint-скрипт для создания симлинков
COPY nginx-entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Выставляем права на конфиги для пользователя bitrix
RUN chown -R bitrix:bitrix /etc/nginx /entrypoint.sh
RUN mkdir -p /var/run && chown bitrix:bitrix /var/run
RUN mkdir -p /var/cache/nginx && chown -R bitrix:bitrix /var/cache/nginx

USER bitrix

ENTRYPOINT ["/entrypoint.sh"]