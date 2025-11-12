# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies required for PostgreSQL extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy project files into the container
COPY . /var/www/html/

# Set working directory to your app folder
WORKDIR /var/www/html/Restro

# Expose Render's required port
EXPOSE 10000

# Start PHP development server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
