FROM php:8.3-fpm

WORKDIR /var/www/html/app

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libssl-dev \
    postgresql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

EXPOSE 9000

CMD ["sh", "-lc", "mkdir -p /var/www/html/app/storage/framework/views /var/www/html/app/storage/framework/cache/data /var/www/html/app/storage/framework/sessions /var/www/html/app/storage/logs /var/www/html/app/bootstrap/cache && chmod -R 0777 /var/www/html/app/storage /var/www/html/app/bootstrap/cache && php-fpm"]