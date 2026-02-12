# 🚀 Laravel 12 Production Deployment Guide

This document explains how to deploy this Laravel 12.50 application using:

- PHP 8.4
- Node 24.12
- Docker & Docker Compose
- Amazon Linux 2023 (EC2)
- Nginx Reverse Proxy
- Let's Encrypt SSL

---

# 🧱 Tech Stack

- Laravel 12.50
- PHP 8.4
- Node 24.12 (Vite)
- MySQL 8
- Docker
- Nginx
- AWS EC2 (Amazon Linux 2023)

---

# 🖥️ Server Requirements

- Amazon Linux 2023
- Docker installed
- Docker Compose plugin installed
- Nginx installed
- Git installed
- Domain pointed to EC2 public IP

---

# 🔧 1. Initial Server Setup (Amazon Linux 2023)

Update system:

```bash
sudo dnf update -y
```

Install Docker:

```bash
sudo dnf install docker -y
sudo systemctl enable docker
sudo systemctl start docker
sudo usermod -aG docker ec2-user
```

Log out and back in after adding Docker group.

Install Docker Compose plugin:

```bash
sudo dnf install docker-compose-plugin -y
```

Install Nginx:

```bash
sudo dnf install nginx -y
sudo systemctl enable nginx
```

---

# 📥 2. Clone Repository

```bash
git clone https://github.com/ayeeet/openasset-inventory.git
cd openasset-inventory
```

---

# ⚙️ 3. Configure Environment

Copy environment file:

```bash
cp .env.example .env
```

Edit `.env`:

```
APP_NAME=LaravelApp
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

---

# 🐳 4. Docker Production Build

Build containers:

```bash
docker compose -f docker-compose.prod.yml build
```

Start containers:

```bash
docker compose -f docker-compose.prod.yml up -d
```

Check running containers:

```bash
docker ps
```

---

# 🗄️ 5. Run Migrations

```bash
docker compose exec app php artisan migrate --force
```

(Optional seed)

```bash
docker compose exec app php artisan db:seed --force
```

---

# 🎨 6. Frontend Build (Node 24.12)

This project uses Vite.

If building locally:

```bash
nvm install 24
nvm use 24
npm install
npm run build
```

If using Docker multi-stage build, Node 24 is used inside container.

---

# 📂 7. Storage Setup

Run:

```bash
docker compose exec app php artisan storage:link
```

Ensure correct permissions:

```bash
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

---

# 🌐 8. Nginx Reverse Proxy Configuration

Create config:

```bash
sudo nano /etc/nginx/conf.d/app.conf
```

Example configuration:

```
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

Test Nginx:

```bash
sudo nginx -t
```

Restart:

```bash
sudo systemctl restart nginx
```

---

# 🔒 9. Enable SSL (Let's Encrypt)

Install Certbot:

```bash
sudo dnf install certbot python3-certbot-nginx -y
```

Generate certificate:

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Test renewal:

```bash
sudo certbot renew --dry-run
```

---

# 🔄 Deployment Workflow (After Code Updates)

```bash
git pull origin main
docker compose -f docker-compose.prod.yml build
docker compose -f docker-compose.prod.yml up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

---

# 🛠️ Useful Commands

View logs:

```bash
docker compose logs -f
```

Restart containers:

```bash
docker compose restart
```

Stop containers:

```bash
docker compose down
```

---

# 📁 File Upload System

- Asset Agreements are stored in `storage/app/public/agreements`
- Resource Invoices are stored in `storage/app/public/invoices`
- File paths are stored in database
- Access via `storage:link`

---

# 🚀 Production Checklist

- [ ] APP_DEBUG=false
- [ ] SSL enabled
- [ ] Docker containers healthy
- [ ] Database migrated
- [ ] Storage linked
- [ ] Nginx config tested
- [ ] Domain DNS configured

---

# 👨‍💻 Maintainer

Developed and deployed by **Jinhendrix Sore**

---

# 📜 License

Internal proprietary system.

