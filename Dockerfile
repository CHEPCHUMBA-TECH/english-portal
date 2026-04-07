# Use official PHP image
FROM php:8.2-apache

# Enable mod_rewrite for PHP
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Set permissions
RUN chown -R www-data:www-data .

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
