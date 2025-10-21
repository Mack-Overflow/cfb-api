FROM php:8.3-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpng-dev libonig-dev libzip-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql bcmath gd intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Useful PHP-FPM settings (tweak as desired)
RUN { \
      echo "memory_limit=512M"; \
      echo "opcache.enable=1"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.max_accelerated_files=20000"; \
    } > /usr/local/etc/php/conf.d/custom.ini

# www-data owns storage/bootstrap
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
