# Use official PHP + Apache image
FROM php:8.2-apache

# Install PostgreSQL extension and required tools
RUN apt-get update \
 && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy Apache virtual host config
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable our custom site
RUN a2ensite 000-default.conf

# Disable default Apache site
RUN a2dissite 000-default

# Copy application code
COPY . /var/www/html/

# Working directory at web root
WORKDIR /var/www/html

# Add start script that handles Render's dynamic port
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose port (Render sets real $PORT at runtime)
EXPOSE 10000

# Start Apache through our script
CMD ["/start.sh"]
