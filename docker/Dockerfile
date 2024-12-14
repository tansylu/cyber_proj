# Base image
FROM php:8.0-apache

# Install necessary PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    coreutils \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql

# Enable Apache modules for development
RUN a2enmod rewrite

# Copy source files into the container
WORKDIR /var/www/html
COPY src/ /var/www/html/
COPY uploads/ /var/www/uploads/

# Set permissions for the uploads directory
RUN chmod -R 777 /var/www/uploads

# Expose the default Apache port
EXPOSE 80
