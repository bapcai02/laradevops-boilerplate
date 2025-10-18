#!/bin/bash

# Laravel DevOps Boilerplate - Deployment Script
# This script handles the deployment process for Laravel applications

set -e  # Exit on any error

echo "🚀 Starting deployment process..."

# Configuration
PROJECT_DIR="/var/www/laradevops-boilerplate"
BACKUP_DIR="/var/backups/laradevops"
DATE=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please run this script from the Laravel project root."
    exit 1
fi

print_status "Deployment started at $(date)"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup current version (if exists)
if [ -d "$PROJECT_DIR" ]; then
    print_status "Creating backup of current version..."
    tar -czf "$BACKUP_DIR/backup_$DATE.tar.gz" -C "$(dirname $PROJECT_DIR)" "$(basename $PROJECT_DIR)" 2>/dev/null || true
    print_status "Backup created: $BACKUP_DIR/backup_$DATE.tar.gz"
fi

# Pull latest changes from Git
print_status "Pulling latest changes from Git..."
git pull origin main

# Install/Update Composer dependencies
print_status "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions
print_status "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear and cache configuration
print_status "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Run database migrations
print_status "Running database migrations..."
php artisan migrate --force

# Clear application cache
print_status "Clearing application cache..."
php artisan cache:clear
php artisan queue:restart

# Restart services (if using systemd)
if command -v systemctl &> /dev/null; then
    print_status "Restarting services..."
    sudo systemctl reload nginx 2>/dev/null || true
    sudo systemctl restart php8.3-fpm 2>/dev/null || true
fi

# Health check
print_status "Performing health check..."
if php artisan --version > /dev/null 2>&1; then
    print_status "✅ Application is healthy"
else
    print_error "❌ Application health check failed"
    exit 1
fi

print_status "🎉 Deployment completed successfully!"
print_status "Deployment finished at $(date)"

# Optional: Clean up old backups (keep last 5)
print_status "Cleaning up old backups..."
ls -t $BACKUP_DIR/backup_*.tar.gz | tail -n +6 | xargs -r rm

print_status "Deployment process completed successfully! 🚀"
