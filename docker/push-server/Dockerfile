FROM node:20.19.2-alpine

WORKDIR /opt/push-server

# Это исходный код push-server
COPY ./opt/ /opt/push-server/

# Устанавливаем зависимости (production)
RUN npm install --production

# Копируем шаблоны конфигов и упрощённый скрипт-обёртку
COPY ./etc/ /etc/push-server/
COPY ./push-server-multi.sh /opt/push-server/push-server-multi.sh

RUN apk add --no-cache dos2unix

# Конвертируем скрипт в Unix-формат
RUN dos2unix /opt/push-server/push-server-multi.sh

# Делаем скрипт исполняемым
RUN chmod +x /opt/push-server/push-server-multi.sh