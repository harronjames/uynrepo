#!/bin/sh
set -eu

cd /var/www

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public \
  bootstrap/cache

chmod -R ug+rw storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

if [ "${DB_CONNECTION:-}" = "mysql" ] && [ -n "${DB_HOST:-}" ] && [ -n "${DB_PORT:-}" ]; then
    echo "Waiting for database..."
    tries=0
    until php -r '
        $host = getenv("DB_HOST");
        $port = (int) getenv("DB_PORT");
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);
        if ($connection) { fclose($connection); exit(0); }
        exit(1);
    '; do
        tries=$((tries + 1))
        if [ "$tries" -ge 45 ]; then
            echo "Database is not reachable after 45 attempts."
            exit 1
        fi
        sleep 2
    done
fi

if [ -z "${APP_KEY:-}" ]; then
    echo "WARNING: APP_KEY is empty. Set it in deploy.env before production use."
fi

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    php artisan migrate --force --ansi || echo "migrate failed (non-fatal on first boot race)"
    php artisan storage:link --ansi 2>/dev/null || true
    php artisan config:cache --ansi 2>/dev/null || true
    php artisan route:cache --ansi 2>/dev/null || true
    php artisan view:cache --ansi 2>/dev/null || true
fi

exec "$@"
