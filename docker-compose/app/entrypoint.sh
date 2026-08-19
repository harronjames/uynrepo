#!/bin/sh
set -eu

cd /var/www

if [ ! -f .env ] && [ -f .env.local ]; then
    cp .env.local .env || true
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R ug+rw storage bootstrap/cache 2>/dev/null || true

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ "${DB_CONNECTION:-}" = "mysql" ] && [ -n "${DB_HOST:-}" ] && [ -n "${DB_PORT:-}" ]; then
    echo "Waiting for database connection..."

    tries=0
    until php -r '
        $host = getenv("DB_HOST");
        $port = (int) getenv("DB_PORT");
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);
        if ($connection) {
            fclose($connection);
            exit(0);
        }
        exit(1);
    '; do
        tries=$((tries + 1))

        if [ "$tries" -ge 30 ]; then
            echo "Database is not reachable after 30 attempts."
            exit 1
        fi

        sleep 2
    done
fi

# APP_KEY already comes from env_file (.env.local). Never rotate it on boot:
# key:generate --force rewrites the env file and fails with Permission denied
# when the bind-mount is owned by root (typical after git pull as root).
if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --ansi --force || echo "APP_KEY missing and key:generate could not write the env file."
else
    echo "APP_KEY is set, skipping key:generate."
fi

php artisan migrate --ansi --force
php artisan storage:link --ansi || true

exec "$@"
