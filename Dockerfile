# ============================================
# BeArtShare - FrankenPHP Production Dockerfile
# ============================================

# Vite asset'leri (ör. 3D galeri / three.js) ayrı aşamada derlenir;
# son imaja yalnızca public/build girer, Node imajda kalmaz.
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

FROM dunglas/frankenphp:1.7-php8.2-alpine

# PHP ayarları
ARG PHP_MEM_LIMIT=256
ARG PHP_UPLOAD_MAX_FILESIZE=50
ARG PHP_POST_MAX_SIZE=50

LABEL maintainer="BeArtShare <info@beartshare.com>"

ENV APP_ENV=production \
    APP_DEBUG=false \
    FRANKENPHP_CONFIG="worker /app/public/index.php" \
    SERVER_NAME=":80"

# Sistem paketleri (sadece gerekli olanlar)
RUN apk update && apk add --no-cache \
    bash \
    supervisor \
    mysql-client \
    curl \
    # GD extension
    freetype freetype-dev \
    libjpeg-turbo libjpeg-turbo-dev \
    libpng libpng-dev \
    libwebp libwebp-dev \
    # Zip extension
    libzip-dev \
    # Intl extension
    icu-dev icu-data-full

# PHP eklentileri (opcache FrankenPHP imajında zaten mevcut)
RUN docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        intl \
        zip \
        exif

# phpredis (session/cache için) — FrankenPHP imajındaki install-php-extensions ile
RUN install-php-extensions redis

# Build dependency temizliği
RUN apk del --no-cache \
    freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev

# PHP konfigürasyonu
RUN echo "memory_limit = ${PHP_MEM_LIMIT}M" > /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = ${PHP_POST_MAX_SIZE}M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_input_time = 120" >> /usr/local/etc/php/conf.d/custom.ini

# OPcache ayarları
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Caddy/FrankenPHP konfigürasyonu
COPY Caddyfile /etc/caddy/Caddyfile
RUN mkdir -p /config/caddy /data/caddy

# Supervisor konfigürasyonu
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Uygulama kodu
WORKDIR /app
COPY . .
COPY --from=assets /app/public/build /app/public/build

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Dosya izinleri
RUN mkdir -p /app/storage/logs /app/storage/framework/{sessions,views,cache} /app/bootstrap/cache && \
    chown -R www-data:www-data /app /config /data && \
    chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 80

# Cronxo agent (zamanlanmış komutlar, ör. saatlik `php artisan rates:update`).
# Uygulamayı (supervisord) alt süreç olarak başlatır, sinyalleri iletir, onun çıkış koduyla çıkar.
# CRONXO_TOKEN imaja yazılmaz; Easypanel > Environment'ta tanımlanır.
ADD https://cronxo.com/download/cronxo-agent-linux-amd64 /usr/local/bin/cronxo-agent
RUN chmod +x /usr/local/bin/cronxo-agent
ENV CRONXO_SERVER=https://cronxo.com

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["cronxo-agent", "exec", "--", "/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
