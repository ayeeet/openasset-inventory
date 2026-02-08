# Cloud Deployment Guides

This guide covers deploying your Dockerized Laravel application to various cloud platforms.

## Table of Contents

- [AWS ECS with Fargate](#aws-ecs-with-fargate)
- [DigitalOcean App Platform](#digitalocean-app-platform)
- [Heroku](#heroku)
- [Google Cloud Run](#google-cloud-run)
- [Azure Container Instances](#azure-container-instances)

---

## AWS ECS with Fargate

### Prerequisites

- AWS Account with permissions to create ECS services
- AWS CLI installed and configured
- Docker image pushed to Amazon ECR

### Steps

1. **Create an ECR Repository**

```bash
aws ecr create-repository --repository-name asset-inventory --region us-east-1
```

2. **Build and Push Docker Image**

```bash
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin <account-id>.dkr.ecr.us-east-1.amazonaws.com

docker build -t asset-inventory .
docker tag asset-inventory:latest <account-id>.dkr.ecr.us-east-1.amazonaws.com/asset-inventory:latest
docker push <account-id>.dkr.ecr.us-east-1.amazonaws.com/asset-inventory:latest
```

3. **Create CloudFormation Stack or Use ECS Console**

See `ecs-task-definition.json` for the task definition template.

4. **Create RDS MySQL Database**

```bash
aws rds create-db-instance \
  --db-instance-identifier asset-inventory-db \
  --db-instance-class db.t3.micro \
  --engine mysql \
  --master-username admin \
  --master-user-password <strong-password> \
  --allocated-storage 20 \
  --region us-east-1
```

5. **Update Environment Variables**

In ECS Task Definition, set:
- `DB_HOST`: RDS endpoint
- `DB_USERNAME`: RDS master username
- `DB_PASSWORD`: RDS password
- `APP_KEY`: Laravel application key
- `APP_URL`: Your domain URL

### Advantages

- Managed Kubernetes-like experience
- Auto-scaling capabilities
- RDS for managed database
- CloudWatch for monitoring

### Cost Estimate

- ECS Fargate: ~$0.035/hour per vCPU, ~$0.0039/hour per GB RAM
- RDS db.t3.micro: ~$30/month
- **Total**: ~$100-150/month for small apps

---

## DigitalOcean App Platform

### Prerequisites

- DigitalOcean Account
- Docker image pushed to Docker Hub or GHCR

### Steps

1. **Push Docker Image**

```bash
docker tag asset-inventory:latest yourusername/asset-inventory:latest
docker push yourusername/asset-inventory:latest
```

2. **Create App via Dashboard**

- Go to DigitalOcean Dashboard → Apps
- Click "Create App" → "Docker Hub" or "GitHub Container Registry"
- Select your repository and image
- Configure:
  - Service Name: `asset-inventory`
  - Port: `80`
  - HTTP Routes: `/`

3. **Add Database**

- In App Platform, click "Create Database"
- Select MySQL 8.0
- Choose cluster name: `asset-inventory-db`

4. **Add Environment Variables**

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxxxxxxxxxx
APP_URL=${APP_DOMAIN}
DB_HOST=${db.HOSTNAME}
DB_DATABASE=asset_inventory
DB_USERNAME=admin
DB_PASSWORD=${db.PASSWORD}
REDIS_HOST=redis-cache.internal
REDIS_PORT=6379
```

5. **Deploy**

- Click "Deploy"
- Monitor deployment in the App Platform dashboard

### Advantages

- Simplest to set up
- Integrated databases
- Auto-scaling available
- GitHub integration for auto-deploy

### Cost Estimate

- App Platform: ~$12/month (basic)
- MySQL Database: ~$15-30/month
- **Total**: ~$30-50/month

### Auto-Deploy from GitHub

1. Connect your GitHub repository
2. Point to main branch
3. Enable "Auto-Deploy on Push"

---

## Heroku

### Prerequisites

- Heroku Account
- Heroku CLI installed
- Docker image

### Steps

1. **Login to Heroku**

```bash
heroku login
```

2. **Create Heroku App**

```bash
heroku create asset-inventory
```

3. **Create MySQL Database (ClearDB)**

```bash
heroku addons:create cleardb:ignite
```

4. **Create Redis Cache (Redis Cloud)**

```bash
heroku addons:create rediscloud:30
```

5. **Configure Environment Variables**

```bash
heroku config:set \
  APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY=base64:xxxxxxxxxxxxx \
  APP_URL=https://asset-inventory.herokuapp.com
```

6. **Deploy using Container Registry**

```bash
heroku container:login
docker build -t registry.heroku.com/asset-inventory/web .
docker push registry.heroku.com/asset-inventory/web
heroku container:release web -a asset-inventory
```

7. **Run Migrations**

```bash
heroku run php artisan migrate --force
```

### Create Procfile

Create `Procfile`:

```
release: php artisan migrate --force
web: vendor/bin/heroku-php-nginx -C docker/nginx/conf.d/default.conf public/
worker: php artisan queue:work --sleep=3 --tries=3
```

### Advantages

- Simplest deployment
- Built-in monitoring
- Easy rollbacks
- Git-based deployments

### Cost Estimate

- Dyno (Basic): $7/month
- ClearDB: $9.99/month
- RedisCloud: Free → $30/month
- **Total**: ~$50/month

---

## Google Cloud Run

### Prerequisites

- Google Cloud Account
- gcloud CLI installed
- Google Cloud Project

### Steps

1. **Create Project**

```bash
gcloud projects create asset-inventory
gcloud config set project asset-inventory
```

2. **Enable APIs**

```bash
gcloud services enable run.googleapis.com cloudbuild.googleapis.com
```

3. **Build and Push to Container Registry**

```bash
gcloud builds submit --tag gcr.io/asset-inventory/app

# Or manually
docker tag asset-inventory:latest gcr.io/asset-inventory/app:latest
docker push gcr.io/asset-inventory/app:latest
```

4. **Create CloudSQL MySQL Instance**

```bash
gcloud sql instances create asset-inventory-db \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region=us-central1
```

5. **Deploy to Cloud Run**

```bash
gcloud run deploy asset-inventory \
  --image gcr.io/asset-inventory/app:latest \
  --platform managed \
  --region us-central1 \
  --set-env-vars DB_HOST=<CLOUDSQL_IP> \
  --allow-unauthenticated
```

6. **Configure Custom Domain**

```bash
gcloud run services update-traffic asset-inventory --to-revisions LATEST=100
gcloud run domain-mappings create --service=asset-inventory --domain=yourdomain.com
```

### Advantages

- Pay per request (scales to zero)
- Built-in CI/CD with Cloud Build
- Auto-scaling
- Free tier available

### Cost Estimate

- Cloud Run: $0.40 per 1M requests (free tier: 2M requests/month)
- CloudSQL: ~$9.88/month (db-f1-micro)
- **Total**: $0-50/month depending on traffic

---

## Azure Container Instances

### Prerequisites

- Azure Account
- Azure CLI installed

### Steps

1. **Create Resource Group**

```bash
az group create --name asset-inventory-rg --location eastus
```

2. **Create Container Registry**

```bash
az acr create --resource-group asset-inventory-rg \
  --name assetinventoryacr --sku Basic
```

3. **Build and Push Image**

```bash
az acr build --registry assetinventoryacr \
  --image asset-inventory:latest .
```

4. **Create MySQL Server**

```bash
az mysql server create \
  --resource-group asset-inventory-rg \
  --name asset-inventory-db \
  --location eastus \
  --admin-user admin \
  --admin-password <password> \
  --sku-name B_Gen5_1
```

5. **Create Container Instance**

```bash
az container create \
  --resource-group asset-inventory-rg \
  --name asset-inventory \
  --image assetinventoryacr.azurecr.io/asset-inventory:latest \
  --cpu 1 --memory 1 \
  --registry-login-server assetinventoryacr.azurecr.io \
  --registry-username <username> \
  --registry-password <password> \
  --environment-variables \
    DB_HOST=asset-inventory-db.mysql.database.azure.com \
    DB_USERNAME=admin \
    DB_PASSWORD=<password> \
  --ports 80 \
  --dns-name-label asset-inventory
```

### Advantages

- Azure integration
- Auto-scaling available
- Container Groups for multi-container deployment
- Managed MySQL

### Cost Estimate

- Container Instance: ~$15-30/month
- MySQL: ~$15-30/month
- **Total**: ~$30-60/month

---

## Comparison Table

| Platform | Setup Time | Monthly Cost | Auto-Scale | Best For |
|----------|-----------|--------------|-----------|----------|
| **AWS ECS** | 30 min | $100-150 | ✓ | Enterprise apps |
| **DigitalOcean** | 10 min | $30-50 | ✓ | Startups |
| **Heroku** | 5 min | $50-80 | ✓ | Quick MVP |
| **Cloud Run** | 15 min | $0-50 | ✓ | Variable traffic |
| **Azure** | 20 min | $30-60 | ✓ | Enterprise |

---

## General Deployment Checklist

- [ ] Generate strong APP_KEY
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Configure all database credentials
- [ ] Set APP_URL to your domain
- [ ] Configure SSL/HTTPS
- [ ] Set up monitoring and logging
- [ ] Configure backups
- [ ] Set up CI/CD pipeline
- [ ] Test deployment in staging first
- [ ] Document deployment procedures
- [ ] Set up error tracking (Sentry)
- [ ] Configure email service (SendGrid, Mailgun)
- [ ] Monitor costs and usage

---

## Useful Resources

- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [12 Factor App](https://12factor.net/)
- [AWS ECS Quickstart](https://docs.aws.amazon.com/AmazonECS/latest/developerguide/getting-started-fargate.html)
- [DigitalOcean App Platform](https://docs.digitalocean.com/products/app-platform/)
- [Heroku Deployment](https://devcenter.heroku.com/articles/getting-started-with-laravel)
