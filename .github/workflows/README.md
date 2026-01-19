# CI/CD Workflow

This directory contains GitHub Actions workflows for continuous integration and deployment.

## Workflows

### `ci.yml` - Continuous Integration

This workflow runs on every push and pull request to `main` and `develop` branches.

#### Backend Tests Job
- **PHP Version**: 8.2
- **Database**: MySQL 8.0
- **Steps**:
  1. Checkout code
  2. Setup PHP with required extensions
  3. Copy environment file and generate app key
  4. Install Composer dependencies
  5. Run database migrations
  6. Run Laravel Pint (code style check)
  7. Run PHPStan (static analysis, level 5)
  8. Run PHPUnit tests in parallel

#### Frontend Lint Job
- **Node Version**: 20
- **Steps**:
  1. Checkout code
  2. Setup Node.js with npm cache
  3. Install dependencies
  4. Run ESLint
  5. Check Prettier formatting

## Static Analysis

### PHPStan
- **Level**: 5 (moderate strictness)
- **Configuration**: `backend/phpstan.neon`
- **Memory Limit**: 1GB

PHPStan analyzes the codebase for:
- Type errors
- Undefined methods/properties
- Potential bugs
- Code quality issues

### Laravel Pint
Laravel Pint enforces PSR-12 coding standards and Laravel best practices.

## Test Coverage

The CI runs comprehensive tests covering:

### Authentication (`AuthTest.php`)
- User login/logout
- Password change
- Role-based access control
- Validation edge cases

### Posts (`PostTest.php`, `PostAuthorizationTest.php`)
- CRUD operations
- Authorization (writers can only edit their own posts, admins can edit any)
- Draft vs published visibility
- Category filtering

### Users (`UserTest.php`)
- Admin-only user management
- User creation/update/deletion
- Role assignment
- Validation

### Categories (`CategoryTest.php`)
- Public listing/viewing
- Writer/admin creation/editing
- Validation

## Running Locally

### Backend Tests
```bash
cd backend
composer install
php artisan migrate
php artisan test
```

### Static Analysis
```bash
cd backend
vendor/bin/phpstan analyse
vendor/bin/pint --test
```

### Frontend Linting
```bash
cd frontend
npm install
npm run lint
```

## Setup Script

Run `./setup-ci-tests.sh` to automatically:
- Create PHPStan configuration
- Create comprehensive test files
- Create CategoryFactory if missing
- Enhance existing AuthTest

