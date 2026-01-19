FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    autoconf \
    g++ \
    make \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure OPcache for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create non-root user for security
RUN addgroup -g 1000 www && \
    adduser -D -s /bin/sh -u 1000 -G www www

# Copy existing application directory permissions
RUN chown -R www:www /var/www/html

# Change current user to www
USER www

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php -r "echo 'OK';" || exit 1

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]


