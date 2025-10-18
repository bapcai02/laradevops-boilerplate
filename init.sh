#!/bin/bash

# Laravel DevOps Boilerplate - Initialization Script
# This script sets up the development environment

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

print_header() {
    echo -e "${BLUE}[SETUP]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_header "🚀 Laravel DevOps Boilerplate Setup"
print_status "Starting initialization process..."

# Create src directory if it doesn't exist
if [ ! -d "src" ]; then
    print_status "Creating src directory..."
    mkdir -p src
fi

# Copy environment files
print_status "Setting up environment files..."

if [ ! -f "src/.env" ]; then
    if [ -f "env.local" ]; then
        cp env.local src/.env
        print_status "Copied env.local to src/.env"
    elif [ -f "env.example" ]; then
        cp env.example src/.env
        print_status "Copied env.example to src/.env"
    else
        print_warning "No environment file found. You'll need to create src/.env manually."
    fi
else
    print_status "src/.env already exists, skipping..."
fi

# Check if Laravel project exists in src directory
if [ ! -f "src/composer.json" ]; then
    print_warning "Laravel project not found in src/ directory."
    print_status "You need to create a Laravel project in the src/ directory first."
    print_status "Run: composer create-project laravel/laravel src"
    print_status "Or copy your existing Laravel project to the src/ directory."
    print_status "Then run this script again."
    exit 1
fi

# Build and start Docker containers
print_status "Building and starting Docker containers..."
docker-compose down --remove-orphans 2>/dev/null || true
docker-compose build --no-cache
docker-compose up -d

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 10

# Check if containers are running
if ! docker-compose ps | grep -q "Up"; then
    print_error "Failed to start containers. Check the logs with: docker-compose logs"
    exit 1
fi

# Install Composer dependencies
print_status "Installing Composer dependencies..."
docker-compose exec app composer install --no-interaction

# Generate application key if not exists
print_status "Generating application key..."
docker-compose exec app php artisan key:generate

# Set proper permissions
print_status "Setting proper permissions..."
docker-compose exec app chmod -R 755 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

# Run database migrations
print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Clear and cache configuration
print_status "Clearing and caching configuration..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan view:cache

# Clear application cache
print_status "Clearing application cache..."
docker-compose exec app php artisan cache:clear

print_header "✅ Setup completed successfully!"
echo ""
print_status "🌐 Your Laravel application is now running at:"
print_status "   http://localhost:8080"
echo ""
print_status "📊 Service Status:"
docker-compose ps
echo ""
print_status "🔧 Useful commands:"
print_status "   View logs: docker-compose logs -f"
print_status "   Stop services: docker-compose down"
print_status "   Restart services: docker-compose restart"
print_status "   Access app container: docker-compose exec app bash"
print_status "   Access database: docker-compose exec mysql mysql -u laradevops_user -p laradevops"
echo ""
print_status "🎉 Happy coding!"
