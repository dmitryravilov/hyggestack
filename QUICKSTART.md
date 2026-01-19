# Quick Start Guide

Get HyggeStack running in minutes!

## Prerequisites

- Docker & Docker Compose
- Git

## One-Command Setup

```bash
make setup
# or
./setup.sh
```

This automatically:
- ✅ Checks prerequisites
- ✅ Creates environment files
- ✅ Starts all Docker services
- ✅ Installs dependencies
- ✅ Runs migrations and seeders
- ✅ Builds frontend assets

**Access the application:**
- Frontend: http://localhost:8080
- Admin: http://localhost:8080/admin/login
- API: http://localhost:8080/api/v1

**Default Credentials:**
- **Admin**: `admin@hyggestack.local` / `password`
- **Writer**: `emma@hyggestack.local` / `password`

## Manual Setup

If you prefer step-by-step:

### 1. Clone Repository

```bash
git clone git@github.com:dmitryravilov/hyggestack.git
cd hyggestack
```

### 2. Configure Environment

```bash
cp .env.example .env
cp backend/.env.example backend/.env
```

### 3. Start Services

```bash
docker-compose up -d
```

### 4. Setup Backend

```bash
docker-compose exec laravel composer install
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan migrate --seed
```

### 5. Setup Frontend

```bash
docker-compose exec vue npm install
docker-compose exec vue npm run build
```

## Development Commands

### Using Make (Recommended)

```bash
make help          # Show all commands
make start         # Start services
make stop          # Stop services
make logs          # View all logs
make logs-laravel  # Laravel logs only
make logs-vue      # Vue logs only
make test          # Run backend tests
make build         # Build frontend
make migrate       # Run migrations
make shell-laravel # Laravel tinker
make clean         # Stop and remove volumes
```

### Using Docker Compose

```bash
# View logs
docker-compose logs -f laravel
docker-compose logs -f vue

# Run tests
docker-compose exec laravel php artisan test

# Access shells
docker-compose exec laravel php artisan tinker
docker-compose exec vue sh
docker-compose exec postgres psql -U hyggestack -d hyggestack

# Manage services
docker-compose up -d      # Start
docker-compose down       # Stop
docker-compose restart    # Restart
docker-compose ps         # Status
```

## Troubleshooting

### Services Won't Start

```bash
docker-compose logs        # Check logs
docker-compose ps          # Check status
docker-compose restart    # Restart services
```

### Database Connection Errors

```bash
docker-compose ps postgres              # Check database status
docker-compose logs postgres           # Check database logs
docker-compose exec laravel php artisan migrate:fresh --seed  # Reset database
```

### Frontend Not Loading

```bash
docker-compose logs vue                # Check Vue logs
docker-compose exec vue npm run build  # Rebuild frontend
```

### Permission Errors

```bash
docker-compose exec laravel chmod -R 775 storage bootstrap/cache
```

## What's Next?

1. **Explore the Frontend**
   - Browse posts
   - Try different themes
   - Test reader mode

2. **Test the API**
   - Login to get a token
   - Create a post
   - Explore endpoints

3. **Read Documentation**
   - [README.md](./README.md) - Full documentation
   - [CONTRIBUTING.md](./CONTRIBUTING.md) - Development guidelines
   - [docs/architecture.md](./docs/architecture.md) - System design

---

**Welcome to HyggeStack!** ☕
