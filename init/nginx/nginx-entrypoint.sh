#!/bin/sh
set -e

mkdir -p /etc/nginx/bx/site_enabled

rm -f /etc/nginx/bx/site_enabled/*.conf

for f in /etc/nginx/bx/site_available/*.conf; do
  [ -e "$f" ] || continue
  ln -sf "$f" "/etc/nginx/bx/site_enabled/$(basename "$f")"
done

ls -l /etc/nginx/bx/site_enabled

exec nginx -g 'daemon off;'