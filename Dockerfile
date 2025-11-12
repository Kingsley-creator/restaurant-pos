# Use official PHP Apache image
FROM php:8.2-apache

# Install PostgreSQL library headers
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy your project files
COPY . /var/www/html/

# Set the working directory to the 'Restro' folder
WORKDIR /var/www/html/Restro

# Expose port 10000 for Render
EXPOSE 10000

# Start PHP built-in server from the Restro folder
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
