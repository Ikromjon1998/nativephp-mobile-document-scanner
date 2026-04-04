# Epic 5: Scan Result DTO

**Priority:** Medium
**Status:** Not Started

## Goal

Provide a typed `ScanResult` DTO that wraps the `DocumentScanned` event payload, giving developers a clean API to access scan results instead of working with raw arrays.

## Background

Currently, `DocumentScanned` event contains raw properties (`$paths`, `$pageCount`, `$outputFormat`). While functional, a dedicated DTO would provide helper methods and better IDE support.

## Acceptance Criteria

- [ ] `ScanResult` readonly class with typed properties
- [ ] Helper methods: `hasPages()`, `hasPdf()`, `firstPage()`, `toArray()`
- [ ] `DocumentScanned` event provides a `result()` method returning `ScanResult`
- [ ] Backward compatible — existing `$paths`, `$pageCount`, `$outputFormat` properties unchanged
- [ ] Tests for all DTO methods
- [ ] Documentation updated

## Implementation Steps

### Step 1: Create ScanResult DTO

Create `src/Data/ScanResult.php`:
```php
final readonly class ScanResult
{
    /**
     * @param array<int, string> $paths
     */
    public function __construct(
        public array $paths,
        public int $pageCount,
        public string $outputFormat,
    ) {}

    public function hasPages(): bool
    {
        return $this->pageCount > 0;
    }

    public function hasPdf(): bool
    {
        return $this->outputFormat === 'pdf';
    }

    public function firstPage(): ?string
    {
        return $this->paths[0] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'paths' => $this->paths,
            'pageCount' => $this->pageCount,
            'outputFormat' => $this->outputFormat,
        ];
    }
}
```

### Step 2: Update DocumentScanned Event

Add `result()` convenience method:
```php
public function result(): ScanResult
{
    return new ScanResult(
        paths: $this->paths,
        pageCount: $this->pageCount,
        outputFormat: $this->outputFormat,
    );
}
```

### Step 3: Tests

1. Test `ScanResult` construction, helpers, and `toArray()`
2. Test `DocumentScanned::result()` returns correct `ScanResult`

### Step 4: Documentation

1. Update API reference with `ScanResult` class
2. Add Livewire example using `$event->result()->firstPage()`
