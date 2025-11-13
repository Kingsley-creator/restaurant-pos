#!/bin/bash
set -e

# If Render sets PORT, use it; otherwise default to 10000 for local testing
PORT="${PORT:-10000}"

# Update Apache listen port
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf

# Update virtual host to listen on the chosen port
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
exec apache2-foreground
