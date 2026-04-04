# Contributing

Thanks for your interest in contributing to NativePHP Mobile Document Scanner! This guide will help you get set up and understand the project workflow.

## Prerequisites

- PHP 8.3+
- Composer
- A NativePHP Mobile app for testing native changes on a real device

## Getting Started

1. Fork the repository and clone your fork:

```bash
git clone https://github.com/<your-username>/nativephp-mobile-document-scanner.git
cd nativephp-mobile-document-scanner
```

2. Install dependencies:

```bash
composer install
```

3. Run the full check suite to make sure everything passes:

```bash
composer check
```

## Project Structure

```
src/                        # PHP source code
  DocumentScanner.php       # Main class — bridge calls, config injection
  Validation/               # Input validation (ScanValidator)
  Data/                     # DTOs (ScanOptions)
  Events/                   # Laravel events dispatched by native code
  Contracts/                # Interface definitions
  Facades/                  # Laravel facade
  Enums/                    # OutputFormat enum

resources/
  android/src/              # Kotlin native code (Android)
  ios/Sources/              # Swift native code (iOS)
  js/                       # JavaScript client library

config/                     # Publishable Laravel config
tests/                      # Pest test suite
docs/                       # Documentation
  epics/                    # Feature epic plans
```

## Development Workflow

### Running Quality Checks

```bash
# Run everything (style, refactoring, static analysis, tests)
composer check

# Individual checks
vendor/bin/pint --test          # Code style
vendor/bin/rector --dry-run     # Refactoring suggestions
vendor/bin/phpstan analyse      # Static analysis (level 8)
vendor/bin/pest                 # Tests
vendor/bin/pest --coverage      # Tests with coverage (must be >= 90%)
```

### Fixing Code Style

```bash
composer fix    # Runs pint + rector
```

### Making Changes

1. Create a feature branch from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes — follow the existing architecture:
   - **PHP changes**: Update `src/`, add tests in `tests/Unit/`
   - **Native changes**: Update Kotlin in `resources/android/src/` and Swift in `resources/ios/Sources/`
   - **Bridge changes**: Update `nativephp.json` for new bridge functions or events
   - **JS changes**: Update `resources/js/index.js`

3. Run `composer check` before committing

4. Write a clear commit message describing **why**, not just what

5. Open a PR against `main`

## Architecture

### Bridge Call Flow

```
PHP (DocumentScanner::scan())
  → nativephp_call('DocumentScanner.Scan', $params)
    → Android: DocumentScannerFunctions.Scan.execute()
    → iOS: DocumentScannerFunctions.Scan.execute()
      → Native scanner opens
      → User scans documents
      → Results saved to app storage
      → Event dispatched (DocumentScanned / ScanCancelled / ScanFailed)
        → Livewire #[OnNative] / JS On() listener receives event
```

### Adding a New Bridge Function

1. Add the function entry to `nativephp.json`
2. Implement in Kotlin (`resources/android/src/`)
3. Implement in Swift (`resources/ios/Sources/`)
4. Add PHP method to `DocumentScanner.php` and `DocumentScannerInterface.php`
5. Add to facade docblock in `Facades/DocumentScanner.php`
6. Add JS wrapper in `resources/js/index.js`
7. Add tests in `tests/Unit/`
8. Update documentation

### Adding a New Event

1. Create event class in `src/Events/`
2. Register in `nativephp.json` events array
3. Dispatch from both Android and iOS native code
4. Add JS event constant in `resources/js/index.js`
5. Add tests and update docs

## Testing Native Code

Native code (Kotlin/Swift) can only be tested on a real device:

1. Add this plugin to a NativePHP Mobile app via composer
2. Build and run: `php artisan native:run android` or `php artisan native:run ios`
3. Test the scanning flow end-to-end

## Code Quality Standards

- All PHP files must have `declare(strict_types=1)`
- PHPStan level 8 must pass with zero errors
- Test coverage must be >= 90%
- Pint and Rector must pass without changes
