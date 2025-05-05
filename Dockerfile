# Use an official PHP-Apache image
FROM php:7.4-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd zip
# Enable required PHP extensions
RUN docker-php-ext-enable gd pdo_mysql mysqli pdo sodium zip

# Install Composer
RUN apt-get update && apt-get install -y curl unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Set file permissions
RUN chown -R www-data:www-data /var/www/html

#composer install
WORKDIR /var/www/html/laravel
RUN composer install
RUN php artisan config:clear | php artisan view:clear |  php artisan route:clear | php artisan cache:clear

# Expose Apache port
EXPOSE 80

