#!/bin/sh
set -eu

cd "$(dirname "$0")"

echo "==> Pull latest code"
git pull --ff-only origin main

echo "==> Rebuild & restart containers"
docker compose build app
docker compose up -d --remove-orphans

echo "==> Clear Laravel caches inside app container"
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan migrate --force

echo "==> Fix old contact e-mails in database (if any)"
docker compose exec -T app php artisan content:fix-contact-email

echo "==> Done. Test:"
echo "    - https://umzugland.at/datenschutz"
echo "    - https://umzugland.at/impressum"
echo "If Cloudflare is enabled, purge cache for the site."
