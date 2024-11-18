FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    vim \
    unzip \
    git \
    curl \
    cron \
    htop \
    zip \
    && pecl install redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install opcache \
    && docker-php-ext-enable opcache \
    && docker-php-ext-enable redis

RUN apt-get clean && rm -rf /var/lib/apt/lists/*


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN useradd -G www-data,root -u 1000 -d /home/www www
RUN mkdir -p /home/www/.composer && \
    chown -R www:www /home/www

WORKDIR /var/www

EXPOSE 9000

RUN chown -R www-data:www-data /var/www

USER www

CMD ["php-fpm"]