#!/bin/bash
set -e

HOST_UID=${HOST_UID:-1000}
HOST_GID=${HOST_GID:-1000}

# Create group and user if they don't exist
if ! getent group $HOST_GID > /dev/null 2>&1; then
    groupadd -g $HOST_GID host-user || true
fi

if ! getent passwd $HOST_UID > /dev/null 2>&1; then
    useradd -u $HOST_UID -g $HOST_GID -m -s /bin/bash host-user || true
fi

# Update PHP-FPM pool configuration with correct user/group
if [ -f /usr/local/etc/php-fpm.d/www.conf ]; then
    sed -i "s/user = .*/user = host-user/" /usr/local/etc/php-fpm.d/www.conf || true
    sed -i "s/group = .*/group = host-user/" /usr/local/etc/php-fpm.d/www.conf || true
    sed -i "s/listen.owner = .*/listen.owner = host-user/" /usr/local/etc/php-fpm.d/www.conf || true
    sed -i "s/listen.group = .*/listen.group = host-user/" /usr/local/etc/php-fpm.d/www.conf || true
fi

# Copy .env.example to .env if .env doesn't exist
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
        chown $HOST_UID:$HOST_GID /var/www/html/.env 2>/dev/null || true
        echo "Created .env file from .env.example"
    else
        echo "Warning: .env.example not found"
    fi
fi

# Generate application key if not set
if [ -f /var/www/html/.env ] && grep -q "APP_KEY=$" /var/www/html/.env 2>/dev/null; then
    php artisan key:generate --force || true
    chown $HOST_UID:$HOST_GID /var/www/html/.env 2>/dev/null || true
fi

if [ "$1" = "php-fpm" ] || [ "$1" = "php-fpm" ]; then
    exec php-fpm
else
    exec "$@"
fi

