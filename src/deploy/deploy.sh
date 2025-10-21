#!/bin/bash

# Laravel DevOps Boilerplate - Deployment Script
# This script handles the deployment process for Laravel applications

set -e  # Exit on any error

# Change to Laravel project root directory
cd /var/www/html

# Load DB credentials from Laravel .env if present
if [ -f ".env" ]; then
    # Export only DB_* vars
    set -a
    # shellcheck disable=SC2046
    export $(grep -E '^(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=' .env | xargs -r)
    set +a
fi

echo "🚀 Starting deployment process..."

# Configuration
PROJECT_DIR="/var/www/html"
BACKUP_DIR="/var/www/html/storage/backups"
DB_BACKUP_DIR="/var/www/html/storage/backups"
DATE=$(date +%Y%m%d_%H%M%S)
ENVIRONMENT=${ENVIRONMENT:-production}
HISTORY_FILE="storage/logs/deploy_history.jsonl"
DEPLOY_LOG_DIR="storage/logs/deploy"
VERSION_LOG_FILE="storage/logs/deploy/deploy_${DATE}.log"

# Create deploy log directory if it doesn't exist
mkdir -p "$DEPLOY_LOG_DIR"

# Redirect all output to both console and version-specific log file
exec > >(tee -a "$VERSION_LOG_FILE") 2>&1

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
    DB_HOST=${DB_HOST:-mysql}
    DB_PORT=${DB_PORT:-3306}
    DB_DATABASE=${DB_DATABASE:-laradevops}
    DB_USERNAME=${DB_USERNAME:-laradevops_user}
    DB_PASSWORD=${DB_PASSWORD:-laradevops_password}
    
    # Create backup filename
    BACKUP_FILE="$DB_BACKUP_DIR/laravel_${DATE}.sql"
    
    # Perform database backup
    if command -v mysqldump &> /dev/null; then
        # Build password option only if provided to avoid interactive prompt when empty
        PASS_OPT=""
        if [ -n "$DB_PASSWORD" ]; then
            PASS_OPT="-p$DB_PASSWORD"
        fi
        mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" $PASS_OPT --skip-ssl "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null || {
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

# Record deployment start to history
mkdir -p "$(dirname "$HISTORY_FILE")"
START_TIME_EPOCH=$(date +%s)

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
trap 'print_error "Deployment failed at line $LINENO"; send_notification "fail" "Deployment to $ENVIRONMENT failed at line $LINENO"; DURATION=$(($(date +%s) - START_TIME_EPOCH)); echo "{\"id\":\"$DATE\",\"environment\":\"$ENVIRONMENT\",\"status\":\"fail\",\"finished_at\":\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\",\"duration_sec\":$DURATION,\"failed_line\":$LINENO,\"log_file\":\"$VERSION_LOG_FILE\"}" >> "$HISTORY_FILE"; exit 1' ERR

# Pull latest changes from Git (skip in Docker environment)
if [ -d ".git" ]; then
    print_status "Pulling latest changes from Git..."
    git pull origin main
else
    print_status "Skipping Git pull (not a git repository in Docker environment)"
fi

# Install/Update Composer dependencies
print_status "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions (skip in Docker environment)
print_status "Skipping permission changes (handled by Docker)"
# chmod -R 755 storage bootstrap/cache
# chown -R www-data:www-data storage bootstrap/cache

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

# Health check
print_status "Performing health check..."
if php artisan --version > /dev/null 2>&1; then
    print_status "✅ Application is healthy"
    
    # Send success notification
    send_notification "success" "Deployment to $ENVIRONMENT completed successfully!"
    
    print_status "🎉 Deployment completed successfully!"
    print_status "Deployment finished at $(date)"

    # Record deployment success to history
    DURATION=$(( $(date +%s) - START_TIME_EPOCH ))
    echo "{\"id\":\"$DATE\",\"environment\":\"$ENVIRONMENT\",\"status\":\"success\",\"finished_at\":\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\",\"duration_sec\":$DURATION,\"log_file\":\"$VERSION_LOG_FILE\"}" >> "$HISTORY_FILE"
    
    # Optional: Clean up old backups (keep last 5)
    print_status "Cleaning up old backups..."
    ls -t $BACKUP_DIR/backup_*.tar.gz | tail -n +6 | xargs -r rm
    
    print_status "Deployment process completed successfully! 🚀"
else
    print_error "❌ Application health check failed"
    
    # Send failure notification
    send_notification "fail" "Deployment to $ENVIRONMENT failed during health check"
    
    # Record deployment failure to history
    DURATION=$(( $(date +%s) - START_TIME_EPOCH ))
    echo "{\"id\":\"$DATE\",\"environment\":\"$ENVIRONMENT\",\"status\":\"fail\",\"finished_at\":\"$(date -u +%Y-%m-%dT%H:%M:%SZ)\",\"duration_sec\":$DURATION,\"log_file\":\"$VERSION_LOG_FILE\"}" >> "$HISTORY_FILE"
    
    exit 1
fi


