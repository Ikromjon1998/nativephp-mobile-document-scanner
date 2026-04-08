# Epic 13: maxPages Null Convention

**Priority:** Low
**Status:** Not Started

## Goal

Accept `null` as an alternative to `0` for "unlimited pages" in the `maxPages` parameter, making the API more intuitive.

## Background

Currently, `maxPages: 0` means "unlimited pages." While documented, this is a non-obvious convention. Most PHP developers expect `null` to mean "no limit" and `0` to mean "zero pages" (which would be useless). The current behavior requires reading the documentation to understand.

## Acceptance Criteria

- [ ] `maxPages: null` is accepted and treated as unlimited (same as `0`)
- [ ] `maxPages: 0` continues to work as unlimited (backward compatible)
- [ ] `ScanOptions` constructor accepts `?int` for `maxPages`
- [ ] Validation updated to accept `null`
- [ ] Documentation updated to show `null` as the preferred way to express "unlimited"
- [ ] Config default changed from `0` to `null`
- [ ] Tests cover both `null` and `0` as unlimited

## Implementation Steps

### Step 1: Update ScanOptions DTO

```php
public function __construct(
    public ?int $maxPages = null,  // was: int $maxPages = 0
    // ...
)
```

### Step 2: Update Normalization

In `DocumentScanner::scan()`, normalize `null` to `0` before passing to bridge:

```php
$options['maxPages'] = $options['maxPages'] ?? 0;
```

### Step 3: Update Validation

In `ScanValidator::validate()`, allow `null`:

```php
if (isset($options['maxPages']) && $options['maxPages'] !== null && ...) {
```

### Step 4: Update Config and Docs

- Change `default_max_pages` default to `null`
- Update all docs to show `null` as the recommended unlimited value
- Keep mentions that `0` also works for backward compatibility
