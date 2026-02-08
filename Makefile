.PHONY: help build up down logs restart clean migrate seed tinker bash mysql redis test

help: ## Show this help message
	@echo "Asset Inventory - Docker Commands"
	@echo "=================================="
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker images
	docker-compose build

up: ## Start all containers in background
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show logs from all containers
	docker-compose logs -f

logs-app: ## Show logs from app container
	docker-compose logs -f app

logs-mysql: ## Show logs from MySQL container
	docker-compose logs -f mysql

logs-redis: ## Show logs from Redis container
	docker-compose logs -f redis

status: ## Show container status
	docker-compose ps

clean: ## Remove containers and volumes (WARNING: deletes database)
	docker-compose down -v

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate --force

migrate-fresh: ## Reset and re-run all migrations
	docker-compose exec app php artisan migrate:fresh --force

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

migrate-seed: ## Run migrations and seeders
	docker-compose exec app php artisan migrate --force && docker-compose exec app php artisan db:seed

cache-clear: ## Clear application cache
	docker-compose exec app php artisan cache:clear

tinker: ## Start Tinker REPL
	docker-compose exec app php artisan tinker

bash: ## Access app container bash
	docker-compose exec app sh

bash-mysql: ## Access MySQL container bash
	docker-compose exec mysql bash

mysql: ## Access MySQL CLI
	docker-compose exec mysql mysql -u laravel -p asset_inventory

redis: ## Access Redis CLI
	docker-compose exec redis redis-cli

test: ## Run tests
	docker-compose exec app php artisan test

test-feature: ## Run feature tests
	docker-compose exec app php artisan test --testsuite=Feature

test-unit: ## Run unit tests
	docker-compose exec app php artisan test --testsuite=Unit

tinker-exec: ## Execute an expression in Tinker
	docker-compose exec app php artisan tinker --execute="$(EXPR)"

artisan: ## Run Artisan command (usage: make artisan CMD="command")
	docker-compose exec app php artisan $(CMD)

composer: ## Run Composer command (usage: make composer CMD="command")
	docker-compose exec app composer $(CMD)

composer-install: ## Install Composer dependencies
	docker-compose exec app composer install

composer-update: ## Update Composer dependencies
	docker-compose exec app composer update

npm: ## Run NPM command (usage: make npm CMD="command")
	docker-compose exec app npm $(CMD)

npm-install: ## Install NPM dependencies
	docker-compose exec app npm install

npm-build: ## Build frontend assets
	docker-compose exec app npm run build

npm-dev: ## Start development server
	docker-compose exec app npm run dev

backup: ## Create database backup
	docker-compose exec mysql mysqldump -u laravel -p asset_inventory > backup_$(shell date +%Y%m%d_%H%M%S).sql

restore: ## Restore database from backup (usage: make restore FILE=backup.sql)
	docker-compose exec -T mysql mysql -u laravel -p asset_inventory < $(FILE)

health: ## Check service health status
	@echo "Checking service health..."
	@docker-compose exec -T mysql mysqladmin -u laravel -p asset_inventory ping
	@docker-compose exec -T redis redis-cli ping

ps: ## Show running processes
	docker stats

rebuild: ## Rebuild images and restart
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d

setup: ## Initial setup (build, start, migrate, seed)
	docker-compose build
	docker-compose up -d
	docker-compose exec app php artisan migrate --force
	docker-compose exec app php artisan db:seed
	@echo "✓ Setup complete! Visit http://localhost"

info: ## Show environment info
	@echo "Docker Version:"
	@docker --version
	@echo "\nDocker Compose Version:"
	@docker-compose --version
	@echo "\nContainers Running:"
	@docker-compose ps
