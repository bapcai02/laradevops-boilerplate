#!/bin/bash

# Load environment variables from docker.env
if [ -f docker.env ]; then
    export $(cat docker.env | grep -v '^#' | xargs)
fi

# Run docker-compose with environment variables
docker-compose --env-file docker.env up -d

echo "🚀 Services started:"
echo "📱 Web App: http://localhost:${NGINX_PORT:-8081}"
echo "🗄️  phpMyAdmin: http://localhost:${PHPMYADMIN_PORT:-8082}"
echo "🔴 Redis: localhost:${REDIS_PORT:-6379}"
echo "🐬 MySQL: localhost:${MYSQL_PORT:-3307}"
