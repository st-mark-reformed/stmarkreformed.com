############
# Build base
############
FROM php:7.2.33-fpm as base

# Update bash profile
COPY ./docker/.profile /root/.profile
COPY ./docker/.profile /root/.bashrc

RUN apt-get update && apt-get install -my --no-install-recommends \
    git \
    libmagick++-dev \
    libmagickwand-dev \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libwebp-dev \
    libxml2 \
    libxml2-dev \
    libxpm-dev \
    libzip-dev \
    openssl \
    postgresql \
    postgresql-contrib \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/ --with-png-dir=/usr/ --with-jpeg-dir=/usr/ --with-webp-dir=/usr --with-gd \
    && docker-php-ext-install gd \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install bz2 \
    && docker-php-ext-install exif \
    && docker-php-ext-install iconv \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install opcache \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install xmlrpc \
    && docker-php-ext-install zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Add base PHP config
COPY ./docker/php/php-custom-config.ini /usr/local/etc/php/conf.d/php-custom-config.ini
COPY ./docker/php/fpm-www.conf /usr/local/etc/php-fpm.d/www.conf

# Add composer
COPY --from=composer:1.10.5 /usr/bin/composer /usr/bin/composer





###################
# Build prod target
###################
FROM base as prod

# Use custom prod config for PHP
COPY ./docker/php/php-config-prod.ini /usr/local/etc/php/conf.d/php-env-config.ini

# Copy the project files into the image for optimal production performance
COPY . /opt/project





##################
# Build dev target
##################
FROM base as dev

# Use custom dev config for PHP
COPY ./docker/php/php-config-dev.ini /usr/local/etc/php/conf.d/php-env-config.ini

# Install xdebug
RUN pecl install xdebug-2.9.0;
RUN docker-php-ext-enable xdebug;
