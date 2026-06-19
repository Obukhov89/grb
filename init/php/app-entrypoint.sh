#!/bin/bash
set -e

# Устанавливаем зависимости для ulab и других директорий
echo "==> Installing composer dependencies for main app"
composer install --no-interaction --prefer-dist

if [ -d "/var/www/html/protocol_generator" ]; then
  echo "==> Installing composer dependencies for protocol_generator"
  composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html/protocol_generator
fi

if [ -d "/var/www/html/protocol_generator/converter" ]; then
  echo "==> Installing composer dependencies for protocol_generator/converter"
  composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html/protocol_generator/converter
fi

if [ -d "/var/www/html/protocol_generator/Morphos-master" ]; then
  echo "==> Installing composer dependencies for protocol_generator/Morphos-master"
  composer install --no-interaction --prefer-dist --optimize-autoloader --working-dir=/var/www/html/protocol_generator/Morphos-master
fi

# Копируем кастомный файл case_conversion.php в директорию вендора morphos
if [ -f "/var/www/html/case_conversion.php" ] && [ -d "/var/www/html/protocol_generator/Morphos-master/vendor/wapmorgan/morphos/web/" ]; then
  echo "==> Copying custom case_conversion.php to morphos vendor directory"
  cp /var/www/html/case_conversion.php /var/www/html/protocol_generator/Morphos-master/vendor/wapmorgan/morphos/web/case_conversion.php
fi

# Рекурсивно выставляем владельца и группу bitrix для каталога protocol_generator
if [ -d "/var/www/html/protocol_generator" ]; then
  echo "==> Setting owner and group to bitrix:bitrix for /var/www/html/protocol_generator"
  chown -R bitrix:bitrix /var/www/html/protocol_generator
fi

# Запускаем supervisord
echo "==> Starting supervisord"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf