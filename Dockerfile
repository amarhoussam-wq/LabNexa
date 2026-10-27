FROM php:8.2-apache
ARG FILESYSTEM_DISK=local
ARG QUEUE_CONNECTION=sync
ARG MEMCACHED_HOST=127.0.0.1
ARG SESSION_DRIVER=file
ARG SESSION_LIFETIME=120
ARG APP_NAME=Laravel
ARG APP_DEBUG=true
ARG APP_KEY=base64:IXJTLPPc1Puw8ZaQQ4lQ/MtJwdMjo552K+0Z3mhXFEY=
ARG APP_ENV=local
ARG LOG_CHANNEL=stack
ARG DB_HOST=194.34.232.105
ARG LOG_DEPRECATIONS_CHANNEL=null
ARG LOG_LEVEL=debug
ARG DB_CONNECTION=mysql
ARG DB_PORT=5432
ARG DB_USERNAME=root
ARG DB_PASSWORD=vQlykF97sokaam4wJQG237c9YlrlBTHu3YDbWcDyFH0bNs88FMn6upvaBUgxin7j
ARG BROADCAST_DRIVER=log
ARG CACHE_DRIVER=file
ARG DB_DATABASE=labo_dz
ARG MAIL_HOST=smtp.gmail.com
ARG REDIS_PORT=6379
ARG MAIL_MAILER=smtp
ARG MAIL_FROM_ADDRESS=hossahossam484@gmail.com
ARG MAIL_USERNAME=hossahossam484@gmail.com
ARG MAIL_ENCRYPTION=tls
ARG MAIL_PORT=587
ARG REDIS_HOST=127.0.0.1
ARG REDIS_PASSWORD=null
ARG AWS_ACCESS_KEY_ID=
ARG MAIL_FROM_NAME=LabNexa
ARG AWS_DEFAULT_REGION=us-east-1
ARG AWS_SECRET_ACCESS_KEY=
ARG AWS_BUCKET=
ARG AWS_USE_PATH_STYLE_ENDPOINT=false
ARG PUSHER_APP_ID=
ARG PUSHER_APP_KEY=
ARG PUSHER_APP_SECRET=
ARG PUSHER_HOST=
ARG PUSHER_PORT=443
ARG PUSHER_SCHEME=https
ARG PUSHER_APP_CLUSTER=mt1
ARG VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
ARG VITE_PUSHER_HOST=${PUSHER_HOST}
ARG VITE_PUSHER_PORT=${PUSHER_PORT}
ARG VITE_PUSHER_SCHEME=${PUSHER_SCHEME}
ARG VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
ARG SESSION_DOMAIN=ew4sw4sw88k80c4s80c4gcow.194.34.232.105.sslip.io
ARG MAIL_PASSWORD=zwnzmbuhbcyaenml
ARG APP_URL=http://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io
ARG GEMINI_API_KEY=AIzaSyBrhDo1RON1e90GxufQbpeqmCadtfquIUY,AIzaSyA_uRPAHipcCQwwocZRgNeQAEwZ6RKcNKM,AIzaSyA_uRPAHipcCQwwocZRgNeQAEwZ6RKcNKMAIzaSyA_uRPAHipcCQwwocZRgNeQAEwZ6RKcNKM,AIzaSyA_uRPAHipcCQwwocZRgNeQAEwZ6RKcNKM
ARG COOLIFY_URL=https://zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io
ARG COOLIFY_FQDN=zsw8k8c4k8o4kw008skssgkc.194.34.232.105.sslip.io
ARG COOLIFY_BRANCH=main
ARG COOLIFY_RESOURCE_UUID=zsw8k8c4k8o4kw008skssgkc

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    wget \
    libonig-dev \
    libxml2-dev \
    libwebp-dev \
    default-mysql-client \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Update Apache config to use /app/public
ENV APACHE_DOCUMENT_ROOT /app/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy php configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app-php.ini

# Copy application files
COPY . /app

# Create storage directories and set permissions
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    && chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]