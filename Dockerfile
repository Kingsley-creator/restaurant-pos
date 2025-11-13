# Use official PHP + Apache image
FROM php:8.2-apache

# Install PostgreSQL extension and required tools
RUN apt-get update \
 && apt-get install -y libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy custom Apache virtual host config (we'll create this file next)
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy application code into the container
COPY . /var/www/html/

# Ensure Restro is accessible and allow .htaccess changes
# (We'll set DocumentRoot inside 000-default.conf to /var/www/html/Restro)
WORKDIR /var/www/html

# Add startup script that updates Apache ports at container start
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose a port (advisory). The start script will use $PORT or fallback to 10000.
EXPOSE 10000

# Start helper script which adjusts Listen port then launches apache
CMD ["/start.sh"]
