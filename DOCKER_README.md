# Docker Deployment Guide

This application is now containerized with Docker Compose for easy deployment. This guide walks you through setup and deployment.

## Prerequisites

- Docker (v20.10 or higher)
- Docker Compose (v2.0 or higher)
- No need to install PHP, MySQL, or Node.js locally

Download Docker at: https://www.docker.com/products/docker-desktop

## Quick Start

### 1. Generate Application Key

First, generate a unique application key:

```bash
docker run --rm -v $(pwd):/app php:8.2-cli php artisan key:generate --show
```

Copy the generated key (it will look like `base64:xxxxxxxxxxxxxxxxxxxxx`).

### 2. Create Environment File

Create a `.env` file in the project root:

```bash
cp .env.docker .env
```

Edit the `.env` file and:
- Replace `APP_KEY=` with the key generated in step 1
- Update `APP_URL` if needed (default: `http://localhost`)
- Adjust database credentials if desired (keep defaults for local development)

### 3. Start the Application

Build and start all containers:

```bash
docker-compose up -d --build
```

This will:
- Build the Docker image
- Start the Laravel app, MySQL database, and Redis cache
- Run database migrations automatically
- Start the queue worker

### 4. Verify Everything is Running

```bash
docker-compose ps
```

You should see all containers running:
```
CONTAINER ID   IMAGE              STATUS           PORTS
xxxxx          laravel-app        Up 2 minutes     0.0.0.0:80->80/tcp
xxxxx          laravel-mysql      Up 2 minutes     0.0.0.0:3306->3306/tcp
xxxxx          laravel-redis      Up 2 minutes     0.0.0.0:6379->6379/tcp
```

### 5. Access the Application

Open your browser:
- **Web Application**: http://localhost
- **Database (MySQL)**: `localhost:3306`
- **Redis**: `localhost:6379`

## Common Commands

### View Logs

```bash
# All containers
docker-compose logs -f

# Specific service
docker-compose logs -f app       # Laravel app
docker-compose logs -f mysql     # Database
docker-compose logs -f redis     # Cache
```

### Artisan Commands

```bash
# Run any Laravel command
docker-compose exec app php artisan tinker
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan cache:clear
```

### Database Management

```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u laravel -p asset_inventory
# Password: laravel_password

# Create backup
docker-compose exec mysql mysqldump -u laravel -p asset_inventory > backup.sql

# Restore from backup
docker-compose exec -T mysql mysql -u laravel -p asset_inventory < backup.sql
```

### Restart Containers

```bash
# Restart all services
docker-compose restart

# Restart specific service
docker-compose restart app
docker-compose restart mysql
```

### Stop and Remove Everything

```bash
# Stop without removing
docker-compose stop

# Stop and remove containers (data persists in volumes)
docker-compose down

# Stop and remove everything including volumes (WARNING: deletes database data)
docker-compose down -v
```

## Deployment to Production

### Environment Variables

Update `.env` with production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_PASSWORD=strong_secure_password
MYSQL_ROOT_PASSWORD=another_strong_password
```

### Port Configuration

Default port is 80. To use a different port, create a `.env` file or use:

```bash
APP_PORT=8080 docker-compose up -d
```

Or edit `docker-compose.yml`:
```yaml
ports:
  - "8080:80"  # Change to your desired port
```

### Using Environment Files

Create separate `.env` files for different environments:

```bash
# Create production environment file
cp .env .env.production

# Use it with compose
docker-compose --env-file .env.production up -d
```

### SSL/HTTPS Support

For production, configure SSL certificates in Nginx. Update `docker/nginx/conf.d/default.conf` to include:

```nginx
server {
    listen 443 ssl http2;
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    
    # SSL configuration...
}

server {
    listen 80;
    return 301 https://$server_name$request_uri;
}
```

Mount certificates in `docker-compose.yml`:
```yaml
volumes:
  - ./ssl:/etc/nginx/ssl
```

## Troubleshooting

### Container Fails to Start

```bash
# Check logs
docker-compose logs app

# Restart with full output
docker-compose up app
```

### Database Connection Error

```bash
# Ensure MySQL is healthy
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql

# Re-run migrations
docker-compose exec app php artisan migrate --force
```

### Permission Denied Errors

```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
docker-compose exec app chmod -R 755 /app/storage /app/bootstrap/cache
```

### Out of Disk Space

```bash
# Clean up unused Docker resources
docker system prune -a --volumes
```

### Queue Worker Not Processing Jobs

```bash
# Check queue status
docker-compose exec app php artisan queue:failed

# Retry failed jobs
docker-compose exec app php artisan queue:retry all

# Monitor queue
docker-compose exec app php artisan queue:listen
```

## Project Structure

```
.
├── Dockerfile              # Container image definition
├── docker-compose.yml      # Services orchestration
├── .dockerignore          # Files to exclude from image
├── .env.docker            # Docker template environment
├── docker/
│   ├── php/               # PHP-FPM configuration
│   ├── nginx/             # Nginx web server config
│   └── supervisor/        # Process supervisor config
├── app/                   # Application code
├── storage/               # Persistent storage (mounted volume)
└── bootstrap/             # Bootstrap files (mounted volume)
```

## Performance Tips

1. **Enable Debug Mode Only in Development**:
   ```env
   APP_DEBUG=false  # Production
   APP_DEBUG=true   # Development
   ```

2. **Use Redis for Caching**:
   - Already configured in docker-compose.yml
   - Speeds up sessions and cache operations

3. **Monitor Resource Usage**:
   ```bash
   docker stats
   ```

4. **Optimize Images**:
   - Multi-stage build reduces final image size
   - Only production dependencies included

5. **Use Health Checks**:
   - MySQL and Redis include health checks
   - Docker Compose waits for services to be ready

## Support & Documentation

- Laravel: https://laravel.com/docs
- Docker: https://docs.docker.com
- Docker Compose: https://docs.docker.com/compose
- MySQL: https://dev.mysql.com/doc
- Redis: https://redis.io/documentation
- Nginx: https://nginx.org/en/docs

---

For local development without Docker, see the main README.md
