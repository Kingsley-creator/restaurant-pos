# Use an official PHP image with Apache
FROM php:8.2-apache

# Enable necessary PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Copy all project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/Restro

# Expose port 10000 for Render
EXPOSE 10000

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]