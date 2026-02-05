# ============================================
# BeArtShare - FrankenPHP Production Dockerfile
# ============================================

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

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
