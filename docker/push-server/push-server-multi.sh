#!/bin/bash

# Упрощённый менеджер push-server для Docker (только TCP Redis)

# --- Конфигурация (можно переопределять через переменные окружения) ---
HG_DIR="${HG_DIR:-/opt/push-server}"
CFG_DIR="${CFG_DIR:-/etc/push-server}"
BASE_SUB="${BASE_SUB:-801}"
BASE_PUB="${BASE_PUB:-901}"
TMPL_SUB="${TMPL_SUB:-push-server-sub-__PORT__.json}"
TMPL_PUB="${TMPL_PUB:-push-server-pub-__PORT__.json}"
ID_SUB="${ID_SUB:-5}"   # 8010-8015 (6 процессов)
ID_PUB="${ID_PUB:-1}"   # 9010-9011 (2 процесса)
SECURITY_KEY="${SECURITY_KEY:-changeme}"
WS_HOST="${WS_HOST:-127.0.0.1}"
WS_PORT="${WS_PORT:-1337}"
PUBPATH="${PUBPATH:-/bitrix/pub/}"
SUBPATH="${SUBPATH:-/bitrix/sub/}"
SUBWSPATH="${SUBWSPATH:-/bitrix/subws/}"
RESTPATH="${RESTPATH:-/bitrix/rest/}"
REDIS_HOST="${REDIS_HOST:-redis}"
REDIS_PORT="${REDIS_PORT:-6379}"
IP_LIST="${IP_LIST:-127.0.0.1}"

# --- Преобразование IP_LIST в строковый массив для JSON ---
format_ip_list() {
    echo "$IP_LIST" | awk -F',' '{
        for(i=1;i<=NF;i++){
            gsub(/^[ \t]+|[ \t]+$/, "", $i);
            printf "\"%s\"",(i==NF)?$i:$i","
        }
    }'
}

# --- Генерация конфига из шаблона ---
create_config_from_template() {
    template_name="$1"
    service_port="$2"
    dest_file="${CFG_DIR}/$(echo "$template_name" | sed "s/__PORT__/$service_port/")"
    template_file="${CFG_DIR}/${template_name}"

    if [ ! -f "$template_file" ]; then
        echo "Template $template_file not found!"
        exit 1
    fi

    # Получаем корректный массив IP для JSON
    IP_LIST_JSON=$(format_ip_list)

    sed -e "s/__PORT__/$service_port/g;
            s/__SECURITY_KEY__/$SECURITY_KEY/g;
            s/__WS_HOST__/$WS_HOST/g;
            s/__WS_PORT__/$WS_PORT/g;
            s:__PUBPATH__:$PUBPATH:g;
            s:__SUBPATH__:$SUBPATH:g;
            s:__RESTPATH__:$RESTPATH:g;
            s:__SUBWSPATH__:$SUBWSPATH:g;
            s|__REDIS_HOST__|$REDIS_HOST|g;
            s|__REDIS_PORT__|$REDIS_PORT|g;
            s|__IP_LIST__|$IP_LIST_JSON|g;" \
        "$template_file" > "$dest_file"
    echo "Generated $dest_file"
}

# --- Генерация всех нужных конфигов ---
generate_configs() {
    # SUB процессы
    for id in $(seq 0 "$ID_SUB"); do
        service_port="${BASE_SUB}${id}"
        create_config_from_template "$TMPL_SUB" "$service_port"
    done
    # PUB процессы
    for id in $(seq 0 "$ID_PUB"); do
        service_port="${BASE_PUB}${id}"
        create_config_from_template "$TMPL_PUB" "$service_port"
    done
}

# --- Запуск всех процессов push-server ---
start_all() {
    PIDS=""
    # SUB процессы
    for id in $(seq 0 "$ID_SUB"); do
        service_port="${BASE_SUB}${id}"
        config_file="${CFG_DIR}/$(echo "$TMPL_SUB" | sed "s/__PORT__/$service_port/")"
        echo "Starting SUB process on port $service_port"
        node "$HG_DIR/server.js" --config "$config_file" &
        PIDS="$PIDS $!"
    done
    # PUB процессы
    for id in $(seq 0 "$ID_PUB"); do
        service_port="${BASE_PUB}${id}"
        config_file="${CFG_DIR}/$(echo "$TMPL_PUB" | sed "s/__PORT__/$service_port/")"
        echo "Starting PUB process on port $service_port"
        node "$HG_DIR/server.js" --config "$config_file" &
        PIDS="$PIDS $!"
    done

    # Ждём завершения всех процессов (чтобы контейнер не завершился)
    trap "echo 'Stopping all push-server processes'; kill $PIDS; exit 0" SIGTERM SIGINT
    wait $PIDS
}

# --- Основной блок ---
case "$1" in
    "configs")
        generate_configs
        ;;
    "start"|"")
        generate_configs
        start_all
        ;;
    *)
        echo "Usage: $0 [configs|start]"
        exit 1
        ;;
esac