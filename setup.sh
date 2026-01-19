#!/bin/bash

# HyggeStack Setup Script
# This script automates the complete setup process for HyggeStack

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_step() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

# Check prerequisites
check_prerequisites() {
    print_step "Checking Prerequisites"

    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    print_success "Docker is installed"

    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    print_success "Docker Compose is installed"

    # Check if Docker daemon is running
    if ! docker info &> /dev/null; then
        print_error "Docker daemon is not running. Please start Docker first."
        exit 1
    fi
    print_success "Docker daemon is running"
}

# Create .env files if they don't exist
setup_env_files() {
    print_step "Setting up Environment Files"

    # Root .env file
    if [ ! -f .env ]; then
        print_info "Creating .env file..."
        cat > .env << EOF
# HyggeStack Environment Configuration
APP_NAME=HyggeStack
APP_ENV=local
APP_DEBUG=true
APP_PORT=8080

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=hyggestack
DB_USERNAME=hyggestack
DB_PASSWORD=secret

# Redis Configuration
REDIS_HOST=redis
REDIS_PORT=6379

# Frontend Configuration
VITE_API_URL=http://localhost:8080/api/v1
VITE_PORT=5173
EOF
        print_success "Created .env file"
    else
        print_info ".env file already exists, skipping..."
    fi

    # Backend .env file
    if [ ! -f backend/.env ]; then
        print_info "Creating backend/.env file..."
        cat > backend/.env << EOF
APP_NAME=HyggeStack
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=hyggestack
DB_USERNAME=hyggestack
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
EOF
        print_success "Created backend/.env file"
    else
        print_info "backend/.env file already exists, skipping..."
    fi
}

# Start Docker services
start_services() {
    print_step "Starting Docker Services"

    print_info "Building and starting containers..."
    if docker compose version &> /dev/null; then
        docker compose up -d --build
    else
        docker-compose up -d --build
    fi

    print_success "Docker services started"

    # Wait for services to be healthy
    print_info "Waiting for services to be healthy..."
    sleep 5

    # Check if postgres is ready
    print_info "Waiting for PostgreSQL to be ready..."
    max_attempts=30
    attempt=0
    while [ $attempt -lt $max_attempts ]; do
        if docker compose exec -T postgres pg_isready -U hyggestack &> /dev/null || \
           docker-compose exec -T postgres pg_isready -U hyggestack &> /dev/null; then
            print_success "PostgreSQL is ready"
            break
        fi
        attempt=$((attempt + 1))
        sleep 2
    done

    if [ $attempt -eq $max_attempts ]; then
        print_error "PostgreSQL failed to become ready"
        exit 1
    fi
}

# Install backend dependencies
install_backend_deps() {
    print_step "Installing Backend Dependencies"

    print_info "Creating required Laravel directories..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public || true
    else
        docker-compose exec -T --user root laravel mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public || true
    fi

    print_info "Running composer install..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel composer install --no-interaction --prefer-dist --optimize-autoloader
    else
        docker-compose exec -T --user root laravel composer install --no-interaction --prefer-dist --optimize-autoloader
    fi
    print_success "Backend dependencies installed"
}

# Generate application key
generate_app_key() {
    print_step "Generating Application Key"

    print_info "Generating Laravel application key..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel php artisan key:generate --force || true
    else
        docker-compose exec -T --user root laravel php artisan key:generate --force || true
    fi
    print_success "Application key generated"
}

# Set permissions
set_permissions() {
    print_step "Setting Permissions"

    print_info "Setting Laravel storage and cache permissions..."
    # Get the host user ID and group ID to preserve ownership of source files
    HOST_UID=$(id -u)
    HOST_GID=$(id -g)

    if docker compose version &> /dev/null; then
        # Only change ownership of directories that need web server write access
        docker compose exec -T --user root laravel chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
        docker compose exec -T --user root laravel chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
        # Preserve host user ownership for source code directories
        docker compose exec -T --user root laravel chown -R ${HOST_UID}:${HOST_GID} /var/www/html/database /var/www/html/app /var/www/html/config /var/www/html/routes /var/www/html/resources || true
    else
        docker-compose exec -T --user root laravel chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
        docker-compose exec -T --user root laravel chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true
        docker-compose exec -T --user root laravel chown -R ${HOST_UID}:${HOST_GID} /var/www/html/database /var/www/html/app /var/www/html/config /var/www/html/routes /var/www/html/resources || true
    fi
    print_success "Permissions set"
}

# Publish vendor migrations
publish_vendor_migrations() {
    print_step "Publishing Vendor Migrations"

    print_info "Publishing Spatie Permission config..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-config" --force || true
        docker compose exec -T --user root laravel php artisan config:clear || true
    else
        docker-compose exec -T --user root laravel php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-config" --force || true
        docker-compose exec -T --user root laravel php artisan config:clear || true
    fi

    print_info "Publishing Spatie Permission migrations..."
    # Check if migration already exists and has valid content
    MIGRATION_VALID=false
    if docker compose version &> /dev/null; then
        MIGRATION_FILE=$(docker compose exec -T --user root laravel find /var/www/html/database/migrations -name "*_create_permission_tables.php" 2>/dev/null | head -1 || echo "")
        if [ -n "$MIGRATION_FILE" ]; then
            CONTENT=$(docker compose exec -T --user root laravel cat "$MIGRATION_FILE" 2>/dev/null || echo "")
            if [ -n "$CONTENT" ] && echo "$CONTENT" | grep -q "class.*Migration" && echo "$CONTENT" | grep -q "Schema::create"; then
                MIGRATION_VALID=true
            fi
        fi
    else
        MIGRATION_FILE=$(docker-compose exec -T --user root laravel find /var/www/html/database/migrations -name "*_create_permission_tables.php" 2>/dev/null | head -1 || echo "")
        if [ -n "$MIGRATION_FILE" ]; then
            CONTENT=$(docker-compose exec -T --user root laravel cat "$MIGRATION_FILE" 2>/dev/null || echo "")
            if [ -n "$CONTENT" ] && echo "$CONTENT" | grep -q "class.*Migration" && echo "$CONTENT" | grep -q "Schema::create"; then
                MIGRATION_VALID=true
            fi
        fi
    fi

    if [ "$MIGRATION_VALID" = false ]; then
        # Migration doesn't exist or is invalid, publish it
        if docker compose version &> /dev/null; then
            docker compose exec -T --user root laravel php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations" --force || true
        else
            docker-compose exec -T --user root laravel php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations" --force || true
        fi
    fi

    # Always apply fix for cache table issue in permission migration (in case it was overwritten)
    print_info "Applying cache table fix to permission migration..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel php -r "
        \$migrationPath = '/var/www/html/database/migrations';
        \$files = glob(\$migrationPath . '/*_create_permission_tables.php');
        if (!empty(\$files)) {
            \$file = \$files[0];
            \$content = file_get_contents(\$file);

            // Check if fix is already applied
            if (strpos(\$content, 'use Illuminate\\\\Database\\\\QueryException;') !== false && strpos(\$content, 'try {') !== false && strpos(\$content, 'catch (QueryException') !== false) {
                exit(0);
            }

            // Add QueryException import if not present
            if (strpos(\$content, 'use Illuminate\\\\Database\\\\QueryException;') === false) {
                \$content = preg_replace('/(use Illuminate\\\\Database\\\\Migrations\\\\Migration;)/', \"\$1\\nuse Illuminate\\\\Database\\\\QueryException;\", \$content);
            }

            // Match the multi-line cache clearing code
            \$pattern = '/app\(\\'cache\\'\)\\s*\\n\\s*->store\\(config\\(\\'permission\\.cache\\.store\\'\\) != \\'default\\' \\? config\\(\\'permission\\.cache\\.store\\'\\) : null\\)\\s*\\n\\s*->forget\\(config\\(\\'permission\\.cache\\.key\\'\\)\\);/s';

            // If pattern doesn't match, try single line version
            if (!preg_match(\$pattern, \$content)) {
                \$pattern = '/app\\(\\'cache\\'\\)\\s+->store\\(config\\(\\'permission\\.cache\\.store\\'\\) != \\'default\\' \\? config\\(\\'permission\\.cache\\.store\\'\\) : null\\)\\s+->forget\\(config\\(\\'permission\\.cache\\.key\\'\\)\\);/s';
            }

            \$replacement = \"try {\\n            app('cache')\\n                ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)\\n                ->forget(config('permission.cache.key'));\\n        } catch (QueryException \\\$e) {\\n            // Cache table may not exist yet during initial migration\\n            // This is safe to ignore as the cache will be cleared on next request\\n            if (!str_contains(\\\$e->getMessage(), 'does not exist')) {\\n                throw \\\$e;\\n            }\\n        }\";
            \$content = preg_replace(\$pattern, \$replacement, \$content);
            file_put_contents(\$file, \$content);
        }
        " || true
    else
        docker-compose exec -T --user root laravel php -r "
        \$migrationPath = '/var/www/html/database/migrations';
        \$files = glob(\$migrationPath . '/*_create_permission_tables.php');
        if (!empty(\$files)) {
            \$file = \$files[0];
            \$content = file_get_contents(\$file);

            // Check if fix is already applied
            if (strpos(\$content, 'use Illuminate\\\\Database\\\\QueryException;') !== false && strpos(\$content, 'try {') !== false && strpos(\$content, 'catch (QueryException') !== false) {
                exit(0);
            }

            // Add QueryException import if not present
            if (strpos(\$content, 'use Illuminate\\\\Database\\\\QueryException;') === false) {
                \$content = preg_replace('/(use Illuminate\\\\Database\\\\Migrations\\\\Migration;)/', \"\$1\\nuse Illuminate\\\\Database\\\\QueryException;\", \$content);
            }

            // Match the multi-line cache clearing code
            \$pattern = '/app\(\\'cache\\'\)\\s*\\n\\s*->store\\(config\\(\\'permission\\.cache\\.store\\'\\) != \\'default\\' \\? config\\(\\'permission\\.cache\\.store\\'\\) : null\\)\\s*\\n\\s*->forget\\(config\\(\\'permission\\.cache\\.key\\'\\)\\);/s';

            // If pattern doesn't match, try single line version
            if (!preg_match(\$pattern, \$content)) {
                \$pattern = '/app\\(\\'cache\\'\\)\\s+->store\\(config\\(\\'permission\\.cache\\.store\\'\\) != \\'default\\' \\? config\\(\\'permission\\.cache\\.store\\'\\) : null\\)\\s+->forget\\(config\\(\\'permission\\.cache\\.key\\'\\)\\);/s';
            }

            \$replacement = \"try {\\n            app('cache')\\n                ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)\\n                ->forget(config('permission.cache.key'));\\n        } catch (QueryException \\\$e) {\\n            // Cache table may not exist yet during initial migration\\n            // This is safe to ignore as the cache will be cleared on next request\\n            if (!str_contains(\\\$e->getMessage(), 'does not exist')) {\\n                throw \\\$e;\\n            }\\n        }\";
            \$content = preg_replace(\$pattern, \$replacement, \$content);
            file_put_contents(\$file, \$content);
        }
        " || true
    fi
    print_success "Vendor migrations published"
}

# Run migrations and seeders
run_migrations() {
    print_step "Running Database Migrations"

    print_info "Clearing config cache to ensure fresh config is loaded..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel php artisan config:clear || true
    else
        docker-compose exec -T --user root laravel php artisan config:clear || true
    fi

    print_info "Running fresh migrations..."
    if docker compose version &> /dev/null; then
        if ! docker compose exec -T --user root laravel php artisan migrate:fresh --force; then
            print_error "Migrations failed. Attempting to run migrations again..."
            docker compose exec -T --user root laravel php artisan migrate --force
        fi
    else
        if ! docker-compose exec -T --user root laravel php artisan migrate:fresh --force; then
            print_error "Migrations failed. Attempting to run migrations again..."
            docker-compose exec -T --user root laravel php artisan migrate --force
        fi
    fi
    print_success "Migrations completed"

    print_info "Running database seeders..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root laravel php artisan db:seed --force
    else
        docker-compose exec -T --user root laravel php artisan db:seed --force
    fi
    print_success "Database migrations and seeders completed"
}

# Install frontend dependencies
install_frontend_deps() {
    print_step "Installing Frontend Dependencies"

    if docker compose version &> /dev/null; then
        docker compose exec -T --user root vue chown -R node:node /app || true
        docker compose exec -T --user root vue chmod -R 755 /app || true
    else
        docker-compose exec -T --user root vue chown -R node:node /app || true
        docker-compose exec -T --user root vue chmod -R 755 /app || true
    fi

    print_info "Running npm install..."
    if docker compose version &> /dev/null; then
        docker compose exec -T --user root vue npm install
        docker compose exec -T --user root vue chown -R node:node /app/node_modules || true
    else
        docker-compose exec -T --user root vue npm install
        docker-compose exec -T --user root vue chown -R node:node /app/node_modules || true
    fi
    print_success "Frontend dependencies installed"
}

# Build frontend (optional, for production)
build_frontend() {
    print_step "Building Frontend"

    print_info "Building frontend assets..."
    if docker compose version &> /dev/null; then
        docker compose exec -T vue npm run build
    else
        docker-compose exec -T vue npm run build
    fi
    print_success "Frontend built"
}

# Main execution
main() {
    echo -e "\n${GREEN}"
    echo "╔═══════════════════════════════════════════════════════════╗"
    echo "║                                                           ║"
    echo "║           HyggeStack Setup Script                        ║"
    echo "║                                                           ║"
    echo "╚═══════════════════════════════════════════════════════════╝"
    echo -e "${NC}\n"

    check_prerequisites
    setup_env_files
    start_services
    install_backend_deps
    generate_app_key
    set_permissions
    publish_vendor_migrations
    run_migrations
    install_frontend_deps
    build_frontend

    print_step "Setup Complete!"

    echo -e "\n${GREEN}✓${NC} HyggeStack is now ready to use!\n"
    echo -e "Access the application:"
    echo -e "  ${BLUE}Frontend:${NC} http://localhost:8080"
    echo -e "  ${BLUE}API:${NC} http://localhost:8080/api/v1"
    echo -e "\n${GREEN}Default Login Credentials:${NC}"
    echo -e "  ${BLUE}Admin:${NC}  admin@hyggestack.local / password"
    echo -e "  ${BLUE}Author:${NC} emma@hyggestack.local / password"
    echo -e "\n${YELLOW}To view logs:${NC} docker compose logs -f"
    echo -e "${YELLOW}To stop:${NC} docker compose down\n"
}

# Run main function
main

