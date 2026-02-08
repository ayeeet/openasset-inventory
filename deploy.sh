#!/bin/bash
# Docker deployment script for production environments
# Usage: ./deploy.sh [environment] [version]

set -e

ENVIRONMENT=${1:-production}
VERSION=${2:-latest}
APP_NAME="asset-inventory"

echo "=================================="
echo "Asset Inventory Docker Deployment"
echo "=================================="
echo "Environment: $ENVIRONMENT"
echo "Version: $VERSION"
echo ""

# Load environment file
if [ ! -f ".env" ]; then
    echo "ERROR: .env file not found!"
    echo "Please copy .env.docker to .env and update with your values"
    exit 1
fi

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker is not installed"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "ERROR: Docker Compose is not installed"
    exit 1
fi

echo "✓ Docker and Docker Compose are installed"
echo ""

# Select compose file
if [ "$ENVIRONMENT" = "production" ]; then
    COMPOSE_FILE="docker-compose.prod.yml"
else
    COMPOSE_FILE="docker-compose.yml"
fi

if [ ! -f "$COMPOSE_FILE" ]; then
    echo "ERROR: $COMPOSE_FILE not found!"
    exit 1
fi

echo "Using: $COMPOSE_FILE"
echo ""

# Pull latest images
echo "Pulling latest images..."
docker-compose -f "$COMPOSE_FILE" pull

# Stop existing containers
echo "Stopping existing containers..."
docker-compose -f "$COMPOSE_FILE" down

# Build images
echo "Building Docker images..."
docker-compose -f "$COMPOSE_FILE" build

# Start services
echo "Starting services..."
docker-compose -f "$COMPOSE_FILE" up -d

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force

# Clear cache
echo "Clearing application cache..."
docker-compose -f "$COMPOSE_FILE" exec -T app php artisan cache:clear

# Optimize for production
if [ "$ENVIRONMENT" = "production" ]; then
    echo "Optimizing for production..."
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan event:cache
    docker-compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
fi

# Health check
echo "Running health checks..."
health=$(docker-compose -f "$COMPOSE_FILE" ps | grep -c "Up")
if [ "$health" -ge 3 ]; then
    echo "✓ All services are running"
else
    echo "⚠ Warning: Not all services are running"
    docker-compose -f "$COMPOSE_FILE" ps
fi

echo ""
echo "=================================="
echo "✓ Deployment completed successfully!"
echo "=================================="
echo ""
echo "Application URL: $(grep APP_URL .env | cut -d '=' -f2)"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose -f $COMPOSE_FILE logs -f"
echo "  - Run artisan: docker-compose -f $COMPOSE_FILE exec app php artisan <command>"
echo "  - Access database: docker-compose -f $COMPOSE_FILE exec mysql mysql -u laravel -p asset_inventory"
echo ""
