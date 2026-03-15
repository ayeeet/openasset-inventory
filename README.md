# Open Asset Inventory

A robust asset inventory management system built with Laravel.

## 🚀 Production Deployment Guide

This guide outlines the steps to deploy the application to a production server (Linux/Ubuntu recommended).

### 1. Server Requirements

Ensure your server has the following installed:

- **PHP >= 8.2**
- **Composer** (Dependency Manager)
- **Node.js & NPM** (For frontend assets)
- **Database** (MySQL 8.0+ or MariaDB 10.3+)
- **Web Server** (Nginx or Apache)
- **Redis** (Recommended for caching/queues)

Required PHP Extensions:
- BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### 2. Installation

Clone the repository to your web server directory (e.g., `/var/www/open-asset-inventory`):

```bash
cd /var/www
git clone https://github.com/ayeeet/openasset-inventory.git
cd open-asset-inventory
```

Install PHP dependencies (optimized for production):

```bash
composer install --optimize-autoloader --no-dev
```

Install and build frontend assets:

```bash
npm install
npm run build
```

### 3. Configuration

Copy the example environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your production settings:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 4. Database Setup

Run database migrations to set up the schema:

```bash
php artisan migrate --force
```

*(Optional) Seed initial data if this is a fresh install:*
```bash
php artisan db:seed --force
```

### 5. Directory Permissions

Ensure the web server user (usually `www-data`) has write access to storage directories:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 6. Web Server Configuration

#### Nginx Configuration Example

Create a new configuration file at `/etc/nginx/sites-available/open-asset-inventory`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/open-asset-inventory/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and restart Nginx:
```bash
ln -s /etc/nginx/sites-available/open-asset-inventory /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### 7. Optimization (Crucial for Production)

Run these commands to cache configuration and routes for better performance:

```bash
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

> **Note:** If you change any `.env` values or code, you must clear the cache using `php artisan optimize:clear` and re-run these commands.

### 8. Queue Workers (Supervisor)

Install Supervisor to keep your queue workers running:

```bash
apt-get install supervisor
```

Create a configuration file at `/etc/supervisor/conf.d/open-asset-inventory-worker.conf`:

```ini
[program:open-asset-inventory-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/open-asset-inventory/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/open-asset-inventory/storage/logs/worker.log
stopwaitsecs=3600
```

Update Supervisor:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start open-asset-inventory-worker:*
```

### 9. Scheduled Tasks

Add the scheduler to the system cron:

```bash
crontab -e
```

Add the following line:
```cron
* * * * * cd /var/www/open-asset-inventory && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛠️ Updates & Maintenance

To update the application:

1. Pull the latest code: `git pull origin main`
2. Install dependencies: `composer install --no-dev`
3. Build assets: `npm run build`
4. Run migrations: `php artisan migrate --force`
5. Clear and rebuild cache: `php artisan optimize`
6. Restart queues: `php artisan queue:restart`
