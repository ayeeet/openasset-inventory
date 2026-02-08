# Docker & Deployment Documentation

Your Asset Inventory application is now fully containerized! This guide provides an overview of all the Docker-related files and how to use them.

## 📁 Docker Files Overview

### Core Files

| File | Purpose |
|------|---------|
| **Dockerfile** | Builds the application image with PHP 8.2, Nginx, and all dependencies |
| **docker-compose.yml** | Defines development environment (app, MySQL, Redis) |
| **docker-compose.prod.yml** | Production-ready configuration with optimizations |
| **.dockerignore** | Excludes unnecessary files from the Docker image |
| **.env.docker** | Template environment variables for Docker deployment |

### Configuration Files (`docker/` directory)

```
docker/
├── nginx/
│   ├── nginx.conf          # Main Nginx configuration
│   └── conf.d/
│       └── default.conf    # Virtual host configuration
├── php/
│   ├── php-fpm.conf        # PHP-FPM process management
│   └── conf.d/
│       └── opcache.ini     # PHP opcache settings
├── supervisor/
│   └── supervisord.conf    # Process supervisor (PHP-FPM, Nginx, Queue)
├── mysql/
│   └── my.cnf              # MySQL optimization settings
├── entrypoint.sh           # Docker startup script (migrations, etc.)
├── ecs-task-definition.json # AWS ECS deployment template
└── ...
```

## 🚀 Quick Start

### 1. Generate App Key

```bash
docker run --rm -v $(pwd):/app php:8.2-cli php artisan key:generate --show
```

### 2. Create .env File

```bash
cp .env.docker .env
# Edit .env and add the generated APP_KEY
```

### 3. Start Services

```bash
docker-compose up -d --build
```

### 4. Access Application

- **Web**: http://localhost
- **MySQL**: localhost:3306 (user: `laravel`, password: `laravel_password`)
- **Redis**: localhost:6379

## 📚 Documentation Files

### Getting Started

1. **[DOCKER_QUICKSTART.md](DOCKER_QUICKSTART.md)** - Start here!
   - 5-minute setup guide
   - Common troubleshooting
   - Essential commands

2. **[DOCKER_README.md](DOCKER_README.md)** - Comprehensive Docker guide
   - Detailed command reference
   - Deployment options
   - Performance tips
   - Production SSL/HTTPS

3. **[CLOUD_DEPLOYMENT.md](CLOUD_DEPLOYMENT.md)** - Deploy to the cloud
   - AWS ECS/Fargate
   - DigitalOcean App Platform
   - Heroku
   - Google Cloud Run
   - Azure Container Instances

### Deployment Scripts

| Script | Purpose |
|--------|---------|
| **deploy.sh** | Bash deployment script for Linux/Mac |
| **deploy.bat** | Batch deployment script for Windows |
| **Makefile** | Convenient make commands for everything |

## 🎯 What's Included

### Application Stack

- **PHP 8.2 FPM** - Latest PHP with all Laravel requirements
- **Nginx** - High-performance web server
- **MySQL 8.0** - Reliable relational database
- **Redis 7** - Caching and session storage
- **Supervisor** - Process management (PHP-FPM, Nginx, Queue Worker)

### Features

- ✅ Multi-stage Docker build (optimized image size)
- ✅ Auto database migrations on startup
- ✅ Queue worker included
- ✅ Health checks for all services
- ✅ Gzip compression enabled
- ✅ Security headers configured
- ✅ Production-ready Nginx config
- ✅ PHP opcache optimization
- ✅ MySQL performance tuning
- ✅ Graceful shutdown handling

## 🛠️ Common Commands

### Using Makefile (Recommended)

```bash
make help              # View all commands
make up                # Start services
make down              # Stop services
make logs              # View logs
make migrate           # Run migrations
make test              # Run tests
make bash              # Access app shell
make mysql             # Access MySQL CLI
```

### Using docker-compose directly

```bash
# Start in background
docker-compose up -d

# View logs
docker-compose logs -f

# Run artisan
docker-compose exec app php artisan migrate

# Stop services
docker-compose stop
docker-compose down
```

## 🔧 Environment Configuration

### Development (.env.docker → .env)

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
SESSION_DRIVER=redis
CACHE_STORE=redis
```

### Production (docker-compose.prod.yml)

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=notice
```

Use production compose file:

```bash
docker-compose -f docker-compose.prod.yml up -d
```

## 📦 Building Your Own Image

### Local Build

```bash
docker build -t my-laravel-app:latest .
```

### Push to Docker Hub

```bash
docker tag my-laravel-app:latest username/my-laravel-app:latest
docker push username/my-laravel-app:latest
```

### Push to Private Registry

```bash
docker build -t registry.example.com/my-app:latest .
docker push registry.example.com/my-app:latest
```

## ☁️ Deploy to Cloud

Choose your platform:

1. **AWS ECS** (Enterprise)
   ```bash
   # See CLOUD_DEPLOYMENT.md for AWS ECS setup
   ```

2. **DigitalOcean** (Easiest)
   ```bash
   # Push to Docker Hub, then connect on DigitalOcean App Platform
   docker push username/asset-inventory:latest
   ```

3. **Heroku** (Simplest)
   ```bash
   heroku container:push web
   heroku container:release web
   ```

See [CLOUD_DEPLOYMENT.md](CLOUD_DEPLOYMENT.md) for complete guides.

## 🔍 Troubleshooting

### Services Won't Start

```bash
# Check logs
docker-compose logs app

# Common issues:
# - Port 80 in use: APP_PORT=8000 docker-compose up -d
# - MySQL not ready: Wait 5-10 seconds, restart: docker-compose restart mysql
# - Out of memory: Allocate more RAM to Docker
```

### Database Connection Failed

```bash
# Check MySQL is running
docker-compose ps mysql

# Try connecting manually
docker-compose exec mysql mysql -u laravel -p asset_inventory

# Restart MySQL
docker-compose restart mysql
```

### Permission Errors (Linux)

```bash
# Add docker group
sudo usermod -aG docker $USER
newgrp docker

# Or use sudo
sudo docker-compose up -d
```

## 📊 Monitor Your Application

```bash
# Watch resource usage
docker stats

# View logs real-time
docker-compose logs -f

# Check service health
docker-compose ps

# Troubleshoot specific service
docker-compose logs mysql -f
```

## 🔐 Security Checklist

- [ ] Change default database passwords in `.env`
- [ ] Change default MySQL root password
- [ ] Generate strong `APP_KEY`
- [ ] Set `APP_DEBUG=false` for production
- [ ] Configure SSL/HTTPS (see DOCKER_README.md)
- [ ] Set up regular backups
- [ ] Use environment variables for secrets (not in code)
- [ ] Keep Docker images updated
- [ ] Review Nginx security headers

## 💾 Database Backup & Restore

### Create Backup

```bash
docker-compose exec mysql mysqldump -u laravel -p asset_inventory > backup.sql
# Password: laravel_password
```

### Restore Backup

```bash
docker-compose exec -T mysql mysql -u laravel -p asset_inventory < backup.sql
```

## 🔄 Updating the Application

```bash
# Pull latest code
git pull

# Rebuild images with latest code
docker-compose build

# Restart services
docker-compose down
docker-compose up -d
```

## 📈 Performance Optimization

1. **Increase PHP workers:**
   Edit `docker/php/php-fpm.conf`:
   ```ini
   pm.max_children = 30
   pm.start_servers = 10
   ```

2. **Increase MySQL buffer pool:**
   Edit `docker/mysql/my.cnf`:
   ```ini
   innodb_buffer_pool_size = 1024M
   ```

3. **Enable Redis persistence:**
   Add to `docker-compose.yml` Redis service:
   ```yaml
   command: redis-server --appendonly yes
   ```

4. **Monitor with `docker stats`**

## 📚 Learn More

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Docker Compose Documentation](https://docs.docker.com/compose)
- [Nginx Documentation](https://nginx.org/en/docs)
- [MySQL Documentation](https://dev.mysql.com/doc)

## 🎓 Project Structure

```
.
├── Dockerfile                 # Image definition
├── docker-compose.yml         # Development setup
├── docker-compose.prod.yml    # Production setup
├── docker/                    # Configuration files
│   ├── nginx/                 # Web server config
│   ├── php/                   # PHP-FPM config
│   ├── supervisor/            # Process management
│   ├── mysql/                 # Database config
│   └── entrypoint.sh         # Startup script
├── .dockerignore             # Files to exclude
├── .env.docker               # Environment template
├── deploy.sh                 # Linux/Mac deployment
├── deploy.bat                # Windows deployment
├── Makefile                  # Make commands
├── DOCKER_QUICKSTART.md      # Quick start guide
├── DOCKER_README.md          # Comprehensive guide
├── CLOUD_DEPLOYMENT.md       # Cloud deployment
└── ... (application files)
```

## 🚀 Next Steps

1. Read [DOCKER_QUICKSTART.md](DOCKER_QUICKSTART.md) to get started
2. Use `make help` for helpful commands
3. Deploy to production using [CLOUD_DEPLOYMENT.md](CLOUD_DEPLOYMENT.md)
4. Set up CI/CD with GitHub Actions (see `.github/workflows/docker.yml`)

---

**Your application is now containerized and ready for deployment!** 🎉
