#!/bin/sh
set -e

cd /var/www

# Ensure Laravel writable directories exist (bind mount shadows the image)
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Install composer dependencies if vendor is missing (bind mount shadows the image)
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
fi

# php-fpm runs as www-data; ensure vendor is readable by it
chown -R www-data:www-data vendor 2>/dev/null || true

# Build frontend assets if the manifest is missing (public/build is gitignored)
if [ ! -f public/build/manifest.json ]; then
    echo "[entrypoint] Installing npm dependencies..."
    npm ci --no-audit --no-fund || npm install --no-audit --no-fund
    echo "[entrypoint] Building frontend assets..."
    npm run build
fi

# Generate application key if not set
if ! grep -Eq '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "[entrypoint] Generating application key..."
    php artisan key:generate --force
fi

exec "$@"