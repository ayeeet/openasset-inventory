# Docker Quick Start Guide

Get your Asset Inventory application running with Docker in minutes!

## System Requirements

- Docker Desktop (for Windows/Mac) or Docker Engine (for Linux)
- Docker Compose
- 4GB RAM minimum recommended
- 2GB free disk space

## Installation

### Step 1: Copy Environment Configuration

```bash
# Windows CMD
copy .env.docker .env

# Linux/Mac
cp .env.docker .env
```

### Step 2: Generate Laravel Application Key

```bash
# This generates a random key and displays it
docker run --rm -v $(pwd):/app php:8.2-cli php artisan key:generate --show
```

**On Windows PowerShell:**
```powershell
docker run --rm -v ${PWD}:/app php:8.2-cli php artisan key:generate --show
```

Copy the output (looks like `base64:xxxxxxxxxxxxxxxxxxxxx`) and add it to your `.env` file:

```env
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxx
```

### Step 3: Start the Application

```bash
# Build and start all services
docker-compose up -d --build
```

This will:
- Build the Docker image with PHP 8.2, Nginx, and all dependencies
- Download and start MySQL 8.0
- Download and start Redis 7
- Run database migrations automatically
- Start the queue worker

**First run may take 2-5 minutes** as it needs to download images and install dependencies.

### Step 4: Verify Installation

```bash
# Check if all containers are running
docker-compose ps

# Should show:
# - asset-inventory:app (RUNNING)
# - asset-inventory:mysql (RUNNING)
# - asset-inventory:redis (RUNNING)
```

Check the logs:

```bash
docker-compose logs app
```

### Step 5: Access Your Application

Open your browser and navigate to:

```
http://localhost
```

Or with a custom port:

```
http://127.0.0.1:8000  # if APP_PORT=8000
```

## Common First-Time Issues

### Issue: Port 80 Already In Use

**Error:** `error listening on port 80`

**Solution:** Use a different port by setting the environment variable:

```bash
# Windows
set APP_PORT=8000
docker-compose up -d

# Linux/Mac
APP_PORT=8000 docker-compose up -d
```

Then access at `http://localhost:8000`

### Issue: MySQL Connection Refused

**Error:** `Connection refused to MySQL`

**Solution:** 

1. Check MySQL status:
   ```bash
   docker-compose logs mysql
   ```

2. Wait a bit longer (first startup takes 10-15 seconds):
   ```bash
   docker-compose ps
   # Wait until mysql shows "Up" status
   ```

3. Restart MySQL:
   ```bash
   docker-compose restart mysql
   ```

### Issue: Out of Memory

**Error:** `Cannot allocate memory` or service crashes

**Solution:**
- Increase Docker's allocated memory in Docker Desktop Settings
- Recommended: 4GB minimum, 6GB+ for development

### Issue: Permission Denied (Linux)

**Error:** `permission denied while trying to connect to Docker daemon`

**Solution:**
```bash
# Add your user to docker group (logout/login required)
sudo usermod -aG docker $USER
newgrp docker

# Or use sudo
sudo docker-compose up -d
```

## Essential Docker Commands

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis

# Last 100 lines
docker-compose logs --tail=100 app
```

### Run Artisan Commands

```bash
# Run any Laravel command
docker-compose exec app php artisan <command>

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan tinker
```

### Access Containers

```bash
# Laravel app shell
docker-compose exec app sh

# MySQL CLI
docker-compose exec mysql mysql -u laravel -p asset_inventory
# Password when prompted: laravel_password

# Redis CLI
docker-compose exec redis redis-cli
```

### Stop and Cleanup

```bash
# Stop all containers (data persists)
docker-compose stop

# Start again
docker-compose start

# Stop and remove containers
docker-compose down

# Remove everything including data volumes
docker-compose down -v
```

## Development Workflow

### Using Makefile (Recommended)

```bash
# View all available commands
make help

# Start development
make up

# View logs
make logs

# Run tests
make test

# Run migrations
make migrate
```

### Manual Commands

```bash
# Start
docker-compose up -d

# Stop
docker-compose stop

# See status
docker-compose ps

# View logs
docker-compose logs -f
```

## Next Steps

1. **Create a user account:**
   ```bash
   docker-compose exec app php artisan tinker
   # In tinker:
   # >>> \App\Models\User::factory()->create(['email' => 'you@example.com'])
   # >>> exit
   ```

2. **Seed sample data:**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

3. **Access the application:**
   - Navigate to http://localhost
   - Login with your created credentials

4. **Check the Makefile:**
   ```bash
   cat Makefile
   ```
   The Makefile has many helpful commands!

## Important Configuration Files

| File | Purpose |
|------|---------|
| `.env` | Application environment variables |
| `docker-compose.yml` | Services configuration |
| `Dockerfile` | Application image definition |
| `docker/nginx/conf.d/default.conf` | Web server configuration |
| `docker/php/php-fpm.conf` | PHP settings |
| `docker/supervisor/supervisord.conf` | Process management |

## Customization

### Change the Port

Edit `.env`:
```env
APP_PORT=8000
```

Restart:
```bash
docker-compose down
docker-compose up -d
```

### Change Database Credentials

Edit `.env`:
```env
DB_USERNAME=admin
DB_PASSWORD=my_secure_password
MYSQL_ROOT_PASSWORD=root_password
```

**Important:** Do this BEFORE first `docker-compose up`

### Use PostgreSQL Instead of MySQL

Create a new `docker-compose-postgres.yml` or modify existing `docker-compose.yml` to use PostgreSQL image instead.

## Performance Tips

1. **Allocate enough resources:**
   - Docker Desktop: Settings → Resources → Memory: 4GB+

2. **Use Redis for sessions:**
   - Already configured by default
   - Significantly faster than database sessions

3. **Monitor resource usage:**
   ```bash
   docker stats
   ```

4. **Rebuild images occasionally:**
   ```bash
   docker-compose build --no-cache
   ```

## Troubleshooting

### See More Detailed Help

```bash
cat DOCKER_README.md      # Full Docker guide
cat CLOUD_DEPLOYMENT.md   # Production deployment
```

### Debug Mode

Enable debug mode in .env:
```env
APP_DEBUG=true
```

Restart:
```bash
docker-compose restart app
```

Then check logs:
```bash
docker-compose logs -f app | grep -i error
```

### Reset Everything

**WARNING: This removes all data**

```bash
docker-compose down -v
rm .env
cp .env.docker .env
# Edit .env with APP_KEY
docker-compose up -d --build
```

## Getting Help

1. **Docker Documentation:** https://docs.docker.com
2. **Laravel Documentation:** https://laravel.com/docs
3. **Common Issues:** See troubleshooting section above
4. **Logs are your friend:** Always check `docker-compose logs`

## Next Level: Production Deployment

When ready to deploy to production, see:

```bash
cat CLOUD_DEPLOYMENT.md
```

Supported platforms:
- AWS ECS/Fargate
- DigitalOcean App Platform
- Heroku
- Google Cloud Run
- Azure Container Instances

---

**Happy developing!** 🚀

For more detailed information, read the full [DOCKER_README.md](DOCKER_README.md)
