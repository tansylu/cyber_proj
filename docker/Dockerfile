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
WORKDIR /var/www/html/

# Copy composer.json and composer.lock before source files to leverage Docker cache
COPY composer.json composer.lock /var/www/html/

# Install PHP dependencies using Composer
RUN composer install --optimize-autoloader --no-dev

# Copy application files into the container
COPY src/ /var/www/html/
COPY uploads/ /var/www/uploads/
COPY .env /var/www/html/.env 

# List the files to verify installation (optional, remove in production)
RUN ls -la /var/www/html/
RUN cat /var/www/html/.env

# Set proper permissions (avoid 777, set user/group to www-data for security)
RUN chown -R www-data:www-data /var/www/html /var/www/uploads
RUN chmod -R 755 /var/www/html /var/www/uploads

# Expose the default Apache port
EXPOSE 80