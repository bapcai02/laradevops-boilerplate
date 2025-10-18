# Laravel DevOps Boilerplate

A complete Laravel DevOps boilerplate with Docker, CI/CD, and automated deployment capabilities.

## 🚀 Features

- **Docker Environment**: Complete Docker setup with PHP 8.3, Nginx, MySQL 8.0, and Redis
- **CI/CD Pipeline**: Automated testing and deployment via GitHub Actions
- **SSH Deployment**: Automated deployment script for production servers
- **Development Ready**: One-command setup for local development
- **Production Ready**: Optimized configuration for production environments

## 📁 Project Structure

```
laradevops-boilerplate/
├── docker/
│   ├── php/Dockerfile          # PHP 8.3 with required extensions
│   └── nginx/default.conf      # Nginx configuration
├── src/                        # Laravel application
├── deploy/
│   └── deploy.sh              # Production deployment script
├── .github/
│   └── workflows/
│       └── deploy.yml         # CI/CD pipeline
├── docker-compose.yml         # Docker services configuration
├── init.sh                    # Development setup script
├── env.example               # Environment template
├── env.local                 # Local development environment
├── env.production            # Production environment template
└── README.md
```

## 🛠️ Quick Start

### Prerequisites

- Docker and Docker Compose
- Git
- Composer (for initial setup)

### 1. Clone and Setup

```bash
git clone <your-repo-url>
cd laradevops-boilerplate
```

### 2. Initialize Development Environment

```bash
# Make init script executable
chmod +x init.sh

# Run initialization script
./init.sh
```

The script will:
- Create the Laravel project in `src/` directory
- Set up environment files
- Build and start Docker containers
- Install dependencies
- Run database migrations
- Configure the application

### 3. Access Your Application

- **Web Application**: http://localhost:8080
- **Database**: localhost:3306 (MySQL)
- **Redis**: localhost:6379

## 🐳 Docker Services

### Services Overview

| Service | Container | Port | Description |
|---------|-----------|------|-------------|
| **App** | `laradevops_app` | 9000 | PHP 8.3-FPM with Laravel |
| **Nginx** | `laradevops_nginx` | 8080 | Web server and reverse proxy |
| **MySQL** | `laradevops_mysql` | 3306 | Database server |
| **Redis** | `laradevops_redis` | 6379 | Cache and session storage |

### Docker Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Restart specific service
docker-compose restart nginx

# Access app container
docker-compose exec app bash

# Access database
docker-compose exec mysql mysql -u laradevops_user -p laradevops
```

## 🔧 Development

### Environment Configuration

The project includes three environment configurations:

- **`env.example`**: Template for new environments
- **`env.local`**: Local development configuration
- **`env.production`**: Production environment template

### Laravel Commands

```bash
# Access the app container
docker-compose exec app bash

# Run Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller ExampleController
docker-compose exec app php artisan tinker

# Install new packages
docker-compose exec app composer require package/name

# Run tests
docker-compose exec app php artisan test
```

### Database Management

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Create new migration
docker-compose exec app php artisan make:migration create_example_table

# Seed database
docker-compose exec app php artisan db:seed

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

## 🚀 CI/CD Pipeline

### GitHub Actions Workflow

The project includes a complete CI/CD pipeline (`.github/workflows/deploy.yml`) that:

1. **Tests**: Runs PHPUnit tests with MySQL and Redis services
2. **Deploys**: Automatically deploys to production on push to `main` branch

### Required GitHub Secrets

Configure these secrets in your GitHub repository settings:

| Secret | Description | Example |
|--------|-------------|---------|
| `HOST` | Production server IP/hostname | `192.168.1.100` |
| `USERNAME` | SSH username | `deploy` |
| `SSH_KEY` | Private SSH key for server access | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `PROJECT_PATH` | Project path on server | `/var/www/laradevops-boilerplate` |

### Setting up GitHub Secrets

1. Go to your GitHub repository
2. Navigate to Settings → Secrets and variables → Actions
3. Add the required secrets listed above

## 🚀 Production Deployment

### Server Requirements

- Ubuntu 20.04+ or similar Linux distribution
- Docker and Docker Compose
- Git
- SSH access

### Initial Server Setup

1. **Clone the repository**:
   ```bash
   git clone <your-repo-url> /var/www/laradevops-boilerplate
   cd /var/www/laradevops-boilerplate
   ```

2. **Configure environment**:
   ```bash
   cp env.production .env
   # Edit .env with your production settings
   ```

3. **Set up SSH key**:
   ```bash
   # Generate SSH key pair
   ssh-keygen -t rsa -b 4096 -C "deploy@your-domain.com"
   
   # Add public key to authorized_keys
   cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys
   ```

4. **Make deployment script executable**:
   ```bash
   chmod +x deploy/deploy.sh
   ```

### Manual Deployment

```bash
# Run deployment script
./deploy/deploy.sh
```

The deployment script will:
- Pull latest changes from Git
- Install/update Composer dependencies
- Run database migrations
- Clear and cache configuration
- Restart services
- Perform health checks

### Automated Deployment

Deployments are automatically triggered when you push to the `main` branch. The CI/CD pipeline will:

1. Run tests
2. Deploy to production server via SSH
3. Execute the deployment script

## 🔒 Security Considerations

### Production Checklist

- [ ] Change default database passwords
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong `APP_KEY`
- [ ] Configure proper file permissions
- [ ] Set up SSL/TLS certificates
- [ ] Configure firewall rules
- [ ] Set up log monitoring
- [ ] Configure backup strategy

### Environment Security

```bash
# Set proper file permissions
chmod 600 .env
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📊 Monitoring and Logs

### Application Logs

```bash
# View Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log

# View Nginx logs
docker-compose logs nginx

# View all container logs
docker-compose logs -f
```

### Health Checks

The deployment script includes health checks to ensure the application is running correctly after deployment.

## 🛠️ Troubleshooting

### Common Issues

1. **Port already in use**:
   ```bash
   # Check what's using the port
   sudo netstat -tulpn | grep :8080
   
   # Kill the process or change port in docker-compose.yml
   ```

2. **Permission denied**:
   ```bash
   # Fix file permissions
   sudo chown -R $USER:$USER .
   chmod +x init.sh deploy/deploy.sh
   ```

3. **Database connection failed**:
   ```bash
   # Check if MySQL container is running
   docker-compose ps
   
   # Check MySQL logs
   docker-compose logs mysql
   ```

4. **Composer install fails**:
   ```bash
   # Clear Composer cache
   docker-compose exec app composer clear-cache
   
   # Reinstall dependencies
   docker-compose exec app composer install --no-cache
   ```

### Debug Mode

Enable debug mode for troubleshooting:

```bash
# Edit .env file
APP_DEBUG=true
LOG_LEVEL=debug
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

If you encounter any issues or have questions:

1. Check the troubleshooting section above
2. Review the logs for error messages
3. Create an issue in the GitHub repository
4. Check the Laravel documentation for framework-specific issues

## 🔄 Version History

### Version 1.0 (MVP)
- ✅ Docker environment setup
- ✅ CI/CD pipeline with GitHub Actions
- ✅ SSH deployment script
- ✅ Development initialization script
- ✅ Basic Laravel project structure
- ✅ Comprehensive documentation

---

**Happy Coding! 🚀**
