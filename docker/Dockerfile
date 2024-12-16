# Base image
FROM php:8.0-apache

# Install necessary PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    coreutils \
    && docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

# Enable Apache modules for development
RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/

# Copy composer.json and composer.lock before source files to leverage Docker cache
COPY composer.json composer.lock /var/www/

# Install PHP dependencies using Composer
RUN composer install --optimize-autoloader --no-dev

# Copy application files into the container
COPY .env /var/www/

# List the files to verify installation (optional, remove in production)
RUN cat /var/www/.env

# Expose the default Apache port
EXPOSE 80