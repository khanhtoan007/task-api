FROM php:8.3-fpm

ARG HOST_UID=1000
ARG HOST_GID=1000
ENV HOST_USER=host-user

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath \
  && rm -rf /var/lib/apt/lists/*

RUN groupadd -g ${HOST_GID} ${HOST_USER} \
  && useradd -u ${HOST_UID} -g ${HOST_GID} -m -s /bin/bash ${HOST_USER}

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN sed -i "s/^user = .*/user = ${HOST_USER}/" /usr/local/etc/php-fpm.d/www.conf \
 && sed -i "s/^group = .*/group = ${HOST_USER}/" /usr/local/etc/php-fpm.d/www.conf \
 && sed -i "s/^;?listen.owner = .*/listen.owner = ${HOST_USER}/" /usr/local/etc/php-fpm.d/www.conf \
 && sed -i "s/^;?listen.group = .*/listen.group = ${HOST_USER}/" /usr/local/etc/php-fpm.d/www.conf

USER ${HOST_USER}

CMD ["php-fpm"]