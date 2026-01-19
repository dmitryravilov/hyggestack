.PHONY: help setup start stop restart logs clean test build

# Detect docker compose command
DOCKER_COMPOSE := $(shell which docker-compose 2>/dev/null || echo "docker compose")

help: ## Show this help message
	@echo "HyggeStack - Available Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

setup: ## Run complete setup (one command to rule them all!)
	@./setup.sh

start: ## Start all Docker services
	@echo "Starting Docker services..."
	@$(DOCKER_COMPOSE) up -d
	@echo "Services started. Access at http://localhost:8080"

stop: ## Stop all Docker services
	@echo "Stopping Docker services..."
	@$(DOCKER_COMPOSE) down
	@echo "Services stopped"

restart: ## Restart all Docker services
	@echo "Restarting Docker services..."
	@$(DOCKER_COMPOSE) restart
	@echo "Services restarted"

logs: ## View logs from all services
	@$(DOCKER_COMPOSE) logs -f

logs-laravel: ## View Laravel logs
	@$(DOCKER_COMPOSE) logs -f laravel

logs-vue: ## View Vue.js logs
	@$(DOCKER_COMPOSE) logs -f vue

logs-db: ## View database logs
	@$(DOCKER_COMPOSE) logs -f postgres

clean: ## Stop services and remove volumes (fresh start)
	@echo "Stopping services and removing volumes..."
	@$(DOCKER_COMPOSE) down -v
	@echo "Cleanup complete. Run 'make setup' to start fresh."

test: ## Run backend tests
	@echo "Running backend tests..."
	@$(DOCKER_COMPOSE) exec laravel php artisan test

test-frontend: ## Run frontend tests (if configured)
	@echo "Running frontend tests..."
	@$(DOCKER_COMPOSE) exec vue npm test || echo "Frontend tests not configured"

build: ## Build frontend assets
	@echo "Building frontend assets..."
	@$(DOCKER_COMPOSE) exec vue npm run build
	@echo "Frontend built successfully"

install-backend: ## Install backend dependencies
	@echo "Installing backend dependencies..."
	@$(DOCKER_COMPOSE) exec laravel composer install
	@echo "Backend dependencies installed"

install-frontend: ## Install frontend dependencies
	@echo "Installing frontend dependencies..."
	@$(DOCKER_COMPOSE) exec vue npm install
	@echo "Frontend dependencies installed"

migrate: ## Run database migrations
	@echo "Running migrations..."
	@$(DOCKER_COMPOSE) exec laravel php artisan migrate
	@echo "Migrations completed"

migrate-fresh: ## Fresh migration with seeders
	@echo "Running fresh migrations with seeders..."
	@$(DOCKER_COMPOSE) exec laravel php artisan migrate:fresh --seed
	@echo "Fresh migrations completed"

shell-laravel: ## Access Laravel shell (tinker)
	@$(DOCKER_COMPOSE) exec laravel php artisan tinker

shell-vue: ## Access Vue container shell
	@$(DOCKER_COMPOSE) exec vue sh

shell-db: ## Access PostgreSQL shell
	@$(DOCKER_COMPOSE) exec postgres psql -U hyggestack -d hyggestack

status: ## Show status of all services
	@$(DOCKER_COMPOSE) ps


