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

# Fix permissions for mounted volume (only if we're root)
if [ "$(id -u)" = "0" ]; then
    chown -R $HOST_UID:$HOST_GID /var/www/html 2>/dev/null || true
    # Set default permissions for new files
    find /var/www/html -type d -exec chmod 775 {} \; 2>/dev/null || true
    find /var/www/html -type f -exec chmod 664 {} \; 2>/dev/null || true
    
    # Ensure vendor/bin executables have execute permissions
    if [ -d /var/www/html/vendor/bin ]; then
        find /var/www/html/vendor/bin -type f -exec chmod +x {} \; 2>/dev/null || true
        chown -R $HOST_UID:$HOST_GID /var/www/html/vendor/bin 2>/dev/null || true
    fi
    
    # Ensure Laravel storage directories are writable
    if [ -d /var/www/html/storage ]; then
        chmod -R 775 /var/www/html/storage 2>/dev/null || true
        chown -R $HOST_UID:$HOST_GID /var/www/html/storage 2>/dev/null || true
        
        # Create storage subdirectories if they don't exist
        mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
        mkdir -p /var/www/html/storage/logs
        mkdir -p /var/www/html/storage/app/{public,private}
        
        # Fix permissions for storage subdirectories
        chmod -R 775 /var/www/html/storage/framework 2>/dev/null || true
        chmod -R 775 /var/www/html/storage/logs 2>/dev/null || true
        chmod -R 775 /var/www/html/storage/app 2>/dev/null || true
        chown -R $HOST_UID:$HOST_GID /var/www/html/storage 2>/dev/null || true
    fi
    
    # Ensure bootstrap/cache is writable
    if [ -d /var/www/html/bootstrap/cache ]; then
        chmod -R 775 /var/www/html/bootstrap/cache 2>/dev/null || true
        chown -R $HOST_UID:$HOST_GID /var/www/html/bootstrap/cache 2>/dev/null || true
    fi
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

# If running php-fpm, ensure it runs with correct user
if [ "$1" = "php-fpm" ] || [ "$1" = "php-fpm" ]; then
    # PHP-FPM needs to start as root, then it will switch to the configured user
    exec php-fpm
else
    # Execute the main command
    exec "$@"
fi

