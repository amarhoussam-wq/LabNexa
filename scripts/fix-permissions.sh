#!/bin/bash

# Fix permissions for Laravel storage and cache inside Docker/Coolify

echo "Fixing permissions for Laravel..."

# Ensure we are in the right directory
cd /app

# Change ownership to www-data (standard for Apache/Nginx in Docker)
chown -R www-data:www-data storage bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "Permissions fixed successfully!"
