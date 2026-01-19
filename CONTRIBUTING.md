# Contributing to HyggeStack

Thank you for your interest in contributing! This guide will help you get started.

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Maintain the hygge spirit: calm, comfortable, collaborative

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone git@github.com:YOUR_USERNAME/hyggestack.git`
3. Create a branch: `git checkout -b feature/your-feature`
4. Make your changes
5. Test thoroughly
6. Commit with clear messages
7. Push: `git push origin feature/your-feature`
8. Open a Pull Request

## Development Setup

See [README.md](./README.md) for installation instructions. After setup:

```bash
make start    # Start services
make test     # Run tests
make logs     # View logs
```

## Coding Standards

### Backend (Laravel)

- **PSR-12** code formatting
- **Strict typing** (`declare(strict_types=1)`)
- **Meaningful names** for variables, functions, classes
- **Type hints** everywhere possible
- **Comments** for complex logic

**Example:**
```php
<?php

declare(strict_types=1);

namespace App\Services;

class ExampleService
{
    public function doSomething(string $input): array
    {
        // Implementation
    }
}
```

### Frontend (Vue 3)

- **Composition API** (not Options API)
- **Script setup** syntax
- **Consistent naming**: camelCase for variables, PascalCase for components
- **Props validation**
- **Reactive patterns** with `ref()` and `computed()`

**Example:**
```vue
<script setup>
import { ref, computed } from 'vue'

const count = ref(0)
const doubled = computed(() => count.value * 2)
</script>
```

## Testing

### Backend Tests

```bash
make test
# or
docker-compose exec laravel php artisan test
```

- Write tests for new features
- Maintain or improve coverage
- Test edge cases

### Frontend Tests

```bash
docker-compose exec vue npm run test
```

- Test component behavior
- Test user interactions
- Test theme switching

## Pull Request Process

1. **Update documentation** if needed
2. **Add tests** for new features
3. **Ensure all tests pass**
4. **Write clear commit messages**
5. **Link related issues** in PR description

### PR Title Format

```
type(scope): brief description

Examples:
- feat(api): add comment moderation endpoint
- fix(ui): correct theme selector dropdown
- docs(readme): update installation steps
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting, missing semicolons, etc.
- `refactor`: Code restructuring
- `test`: Adding tests
- `chore`: Maintenance tasks

## Reporting Issues

### Bug Reports

Include:
- Steps to reproduce
- Expected behavior
- Actual behavior
- Environment (OS, browser, versions)
- Screenshots (if applicable)

### Feature Requests

Include:
- Clear description
- Use case
- Proposed implementation (if applicable)
- Examples

## Questions?

- Open a discussion on GitHub
- Check existing issues
- Review documentation

## Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md (if applicable)
- Credited in release notes
- Appreciated by the community

---

*Thank you for making HyggeStack cozier!*
