#!/bin/bash

# Laravel DevOps Boilerplate - Deployment Script
# This script handles the deployment process for Laravel applications

set -e  # Exit on any error

echo "🚀 Starting deployment process..."

# Configuration
PROJECT_DIR="/var/www/laradevops-boilerplate"
BACKUP_DIR="/var/backups/laradevops"
DB_BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
ENVIRONMENT=${ENVIRONMENT:-production}

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

# Function to send notifications
send_notification() {
    local type=$1
    local message=$2
    
    if [ -f "artisan" ]; then
        php artisan laradev:notify $type --environment=$ENVIRONMENT --message="$message" 2>/dev/null || true
    fi
}

# Function to backup database
backup_database() {
    print_status "Creating database backup..."
    
    # Create backup directory if it doesn't exist
    mkdir -p $DB_BACKUP_DIR
    
    # Database connection details
    DB_HOST=${DB_HOST:-localhost}
    DB_PORT=${DB_PORT:-3306}
    DB_DATABASE=${DB_DATABASE:-laradevops}
    DB_USERNAME=${DB_USERNAME:-root}
    DB_PASSWORD=${DB_PASSWORD:-}
    
    # Create backup filename
    BACKUP_FILE="$DB_BACKUP_DIR/laravel_${DATE}.sql"
    
    # Perform database backup
    if command -v mysqldump &> /dev/null; then
        mysqldump -h$DB_HOST -P$DB_PORT -u$DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_FILE 2>/dev/null || {
            print_warning "Database backup failed, continuing with deployment..."
            return 1
        }
        print_status "Database backup created: $BACKUP_FILE"
        
        # Compress backup
        gzip $BACKUP_FILE
        print_status "Database backup compressed: $BACKUP_FILE.gz"
        
        # Keep only last 10 backups
        ls -t $DB_BACKUP_DIR/laravel_*.sql.gz | tail -n +11 | xargs -r rm
    else
        print_warning "mysqldump not found, skipping database backup"
    fi
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please run this script from the Laravel project root."
    exit 1
fi

print_status "Deployment started at $(date)"

# Send deployment started notification
send_notification "started" "Deployment to $ENVIRONMENT has started"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup database before deployment
backup_database

# Backup current version (if exists)
if [ -d "$PROJECT_DIR" ]; then
    print_status "Creating backup of current version..."
    tar -czf "$BACKUP_DIR/backup_$DATE.tar.gz" -C "$(dirname $PROJECT_DIR)" "$(basename $PROJECT_DIR)" 2>/dev/null || true
    print_status "Backup created: $BACKUP_DIR/backup_$DATE.tar.gz"
fi

# Trap errors and send failure notification
trap 'print_error "Deployment failed at line $LINENO"; send_notification "fail" "Deployment to $ENVIRONMENT failed at line $LINENO"; exit 1' ERR

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
    
    # Send success notification
    send_notification "success" "Deployment to $ENVIRONMENT completed successfully!"
    
    print_status "🎉 Deployment completed successfully!"
    print_status "Deployment finished at $(date)"
    
    # Optional: Clean up old backups (keep last 5)
    print_status "Cleaning up old backups..."
    ls -t $BACKUP_DIR/backup_*.tar.gz | tail -n +6 | xargs -r rm
    
    print_status "Deployment process completed successfully! 🚀"
else
    print_error "❌ Application health check failed"
    
    # Send failure notification
    send_notification "fail" "Deployment to $ENVIRONMENT failed during health check"
    
    exit 1
fi
