# HyggeStack

> A cozy, comfortable blogging platform inspired by the Danish concept of hygge — where simplicity meets elegance.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.0+-red.svg)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.4+-green.svg)](https://vuejs.org/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://www.docker.com/)

## Features

**Backend (Laravel 12)**
- RESTful API with versioning (`/api/v1`)
- Authentication & Authorization (Laravel Sanctum, role-based access)
- Service + Repository pattern
- Request validation & policies
- OpenAPI documentation (Swagger)
- PHPUnit tests

**Frontend (Vue 3)**
- Vue 3 Composition API with Pinia
- Three hygge-inspired themes (Nordic Minimal, Warm Coffeehouse, Soft Evening)
- Category filtering
- Public blog + Admin dashboard
- Reader mode
- Fully responsive

**DevOps**
- Docker Compose setup
- One-command setup
- Health checks for all services

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

```bash
git clone git@github.com:dmitryravilov/hyggestack.git
cd hyggestack
make setup
```

This single command will:
- Create environment files
- Start all Docker services
- Install dependencies
- Run migrations and seeders
- Build frontend assets

**Access the application:**
- Frontend: http://localhost:8080
- Admin Dashboard: http://localhost:8080/admin/login
- API: http://localhost:8080/api/v1
- API Documentation: http://localhost:8080/api/documentation

### Default Credentials

- **Admin**: `admin@hyggestack.local` / `password`
- **Writer**: `emma@hyggestack.local` / `password`

> Only admin and writer roles can access the admin area. Public registration is disabled.

## Project Structure

```
HyggeStack/
├── backend/          # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/  # Versioned API controllers
│   │   ├── Services/                 # Business logic
│   │   ├── Repositories/             # Data access
│   │   └── Models/                   # Eloquent models
│   ├── database/migrations/          # Database schema
│   └── tests/                        # PHPUnit tests
├── frontend/         # Vue 3 SPA
│   ├── src/
│   │   ├── components/               # Reusable components
│   │   ├── views/                    # Page components
│   │   ├── stores/                   # Pinia stores
│   │   └── router/                   # Vue Router
│   └── public/
├── docker/           # Docker configurations
└── docker-compose.yml
```

## Usage

### Public Blog
- Browse posts by category using the header selector
- Read posts without authentication
- Switch themes using the theme selector

### Admin Dashboard
Access at `/admin/login` (admin/writer roles only).

**Features:**
- **Users** (Admin only): Create, edit, delete users
- **Posts**: Create, edit, delete posts
- **Categories**: Manage categories

### Themes
Choose from three presets:
- **Nordic Minimal** — Clean, light, minimal
- **Warm Coffeehouse** — Warm tones, cozy feel
- **Soft Evening** — Dark mode, gentle on the eyes

Your preference is saved automatically.

## Development

### Available Commands

```bash
make help          # Show all commands
make start         # Start services
make stop          # Stop services
make logs          # View logs
make test          # Run backend tests
make build         # Build frontend assets
make migrate       # Run migrations
make shell-laravel # Laravel tinker
```

See [QUICKSTART.md](./QUICKSTART.md) for detailed development instructions.

### Testing

**Backend:**
```bash
make test
# or
docker-compose exec laravel php artisan test
```

**Frontend:**
```bash
docker-compose exec vue npm run test
```

## API Endpoints

All endpoints are versioned under `/api/v1`:

**Public:**
- `GET /api/v1/posts` — List published posts (supports `?category=slug`)
- `GET /api/v1/posts/{slug}` — Get post by slug
- `GET /api/v1/categories` — List categories
- `GET /api/v1/tags` — List tags

**Protected (Admin/Writer):**
- `POST /api/v1/posts` — Create post
- `PUT /api/v1/posts/{id}` — Update post
- `DELETE /api/v1/posts/{id}` — Delete post
- `POST /api/v1/categories` — Create category
- `PUT /api/v1/categories/{id}` — Update category
- `DELETE /api/v1/categories/{id}` — Delete category

**Admin Only:**
- `GET /api/v1/users` — List users
- `POST /api/v1/users` — Create user
- `PUT /api/v1/users/{id}` — Update user
- `DELETE /api/v1/users/{id}` — Delete user

See API documentation at `/api/documentation` for full details.

## Documentation

- [QUICKSTART.md](./QUICKSTART.md) - Quick start guide
- [CONTRIBUTING.md](./CONTRIBUTING.md) - Contribution guidelines
- [docs/architecture.md](./docs/architecture.md) - Architecture overview
- [docs/api.md](./docs/api.md) - API reference
- [themes/README.md](./themes/README.md) - Theme guide

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](./CONTRIBUTING.md) for guidelines.

## License

MIT License - see [LICENSE](./LICENSE) for details.

---

**Made with ❤️ and ☕**

*"Hygge is about an atmosphere and an experience, rather than about things. It is about being with the people we love. A feeling of home. A feeling that we are safe."* — Meik Wiking

**Repository**: [github.com/dmitryravilov/hyggestack](https://github.com/dmitryravilov/hyggestack)
