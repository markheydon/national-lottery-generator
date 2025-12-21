# Contributing to National Lottery Generator

Thank you for your interest in contributing to the National Lottery Generator! This document provides guidelines for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Submitting Changes](#submitting-changes)
- [Reporting Issues](#reporting-issues)

## Code of Conduct

This project follows a simple code of conduct:
- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Keep discussions professional

## Getting Started

### Prerequisites

- PHP 8.2 or higher (8.4 recommended)
- Docker (for Laravel Sail)
- Git
- Composer
- Basic understanding of Laravel framework

### Setting Up Your Development Environment

1. **Fork and Clone the Repository**
   ```bash
   git clone https://github.com/YOUR-USERNAME/national-lottery-generator.git
   cd national-lottery-generator
   ```

2. **Install Dependencies**
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

3. **Set Up Environment**
   ```bash
   cp .env.example .env
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan key:generate
   ```

4. **Verify Installation**
   ```bash
   ./vendor/bin/sail artisan test
   ```

## Development Workflow

### Creating a Feature or Fix

1. **Create a Branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/issue-description
   ```

2. **Make Your Changes**
   - Write clear, self-documenting code
   - Follow PSR-12 coding standards
   - Add or update tests as needed
   - Update documentation if needed

3. **Test Your Changes**
   ```bash
   # Run tests
   ./vendor/bin/sail artisan test
   
   # Check code style
   ./vendor/bin/sail pint --test
   
   # Fix code style issues
   ./vendor/bin/sail pint
   ```

4. **Commit Your Changes**
   ```bash
   git add .
   git commit -m "Brief description of changes"
   ```

   Write clear commit messages:
   - Use present tense ("Add feature" not "Added feature")
   - Be concise but descriptive
   - Reference issue numbers when applicable

5. **Push and Create Pull Request**
   ```bash
   git push origin feature/your-feature-name
   ```
   Then create a Pull Request on GitHub.

## Coding Standards

### PHP Code Style

This project follows [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards:

- **Always run Laravel Pint** before committing:
  ```bash
  ./vendor/bin/sail pint
  ```

- **Use meaningful names**: Avoid generic names like `$var`, `$method1`, `$temp`
- **Document your code**: Add PHPDoc comments for classes, methods, and complex logic
- **Keep it simple**: Write clear, maintainable code over clever code

### Laravel Best Practices

- Keep controllers thin - move business logic to Services
- Use Laravel's built-in features (validation, caching, etc.)
- Follow Laravel naming conventions
- Use type hints and return types
- Leverage dependency injection

### Example Code Style

```php
<?php

namespace App\Services;

use App\Models\LotteryDraw;
use Illuminate\Support\Collection;

class NumberGenerator
{
    /**
     * Generate lottery numbers based on historical data.
     *
     * @param  Collection  $historicalDraws
     * @param  int  $count
     * @return array
     */
    public function generateNumbers(Collection $historicalDraws, int $count = 6): array
    {
        // Implementation here
        return $numbers;
    }
}
```

## Testing

### Writing Tests

- Write tests for new features and bug fixes
- Follow existing test patterns in the `tests/` directory
- Use descriptive test method names
- Test edge cases and error conditions

### Test Structure

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\NumberGenerator;

class NumberGeneratorTest extends TestCase
{
    /** @test */
    public function it_generates_correct_number_of_lottery_numbers(): void
    {
        $generator = new NumberGenerator();
        $numbers = $generator->generateNumbers(collect(), 6);
        
        $this->assertCount(6, $numbers);
    }
}
```

### Running Tests

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run specific test file
./vendor/bin/sail artisan test tests/Unit/Services/NumberGeneratorTest.php

# Run tests with coverage
./vendor/bin/sail artisan test --coverage
```

## Submitting Changes

### Pull Request Process

1. **Ensure Your PR**:
   - Passes all tests
   - Follows code style guidelines (run Pint)
   - Includes tests for new features
   - Updates documentation if needed
   - Has a clear description of changes

2. **PR Description Should Include**:
   - What changes were made and why
   - How to test the changes
   - Any breaking changes
   - Screenshots (if applicable for UI changes)
   - Related issue numbers (fixes #123)

3. **Review Process**:
   - Maintainers will review your PR
   - Be responsive to feedback
   - Make requested changes in new commits
   - Once approved, maintainers will merge

### PR Title Format

Use clear, descriptive titles:
- `Add: New feature description`
- `Fix: Bug description`
- `Update: Component/feature description`
- `Refactor: What was refactored`
- `Docs: Documentation changes`

## Reporting Issues

### Before Creating an Issue

1. Check if the issue already exists
2. Ensure you're using the latest version
3. Verify it's not an environment-specific problem

### Creating a Good Issue Report

Include:
- **Clear title** describing the issue
- **Description** of the problem
- **Steps to reproduce** (for bugs)
- **Expected behavior**
- **Actual behavior**
- **Environment details** (PHP version, OS, browser if applicable)
- **Screenshots** or error messages (if applicable)

### Issue Templates

**Bug Report:**
```markdown
## Description
Brief description of the bug

## Steps to Reproduce
1. Step one
2. Step two
3. Step three

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- PHP Version: 8.4
- Laravel Version: 12.x
- Browser: Chrome 120
- OS: Ubuntu 22.04
```

**Feature Request:**
```markdown
## Feature Description
Clear description of the proposed feature

## Use Case
Why this feature would be useful

## Proposed Implementation
(Optional) How you think it could be implemented

## Alternatives
Other solutions you've considered
```

## Questions?

If you have questions about contributing:
- Open an issue with the `question` label
- Check existing issues and discussions
- Review the [main README](README.md) and [documentation](docs/)

## License

By contributing, you agree that your contributions will be licensed under the same [GNU General Public License v3.0](LICENSE) that covers this project.

---

Thank you for contributing to National Lottery Generator! 🎰
