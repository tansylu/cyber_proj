# Use official PHP image with Apache as base
FROM php:8.0-apache

# Install necessary PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite module for clean URLs (common in many PHP frameworks)
RUN a2enmod rewrite

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory inside the container
WORKDIR /var/www/

# Copy composer.json and composer.lock first to leverage Docker's layer caching
COPY composer.json composer.lock /var/www/

# Install PHP dependencies using Composer
RUN composer install --optimize-autoloader --no-dev

# Copy the rest of the application files into the container
COPY . /var/www/

# Ensure the 'uploads' directory exists and set the correct file permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads

# Optionally, list the files to verify installation (can be removed in production)
RUN ls -la /var/www/

# Expose the default Apache port (80) for external access
EXPOSE 80

# Set the entry point to run Apache in the foreground
CMD ["apache2-foreground"]
