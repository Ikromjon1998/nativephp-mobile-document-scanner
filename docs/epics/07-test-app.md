# Epic 7: Real Device Test App

**Priority:** Low
**Status:** Not Started

## Goal

Create a debug/test component (like the local-notifications plugin's `NotificationDebug`) that can be added to any NativePHP app to verify scanning works on a real device.

## Background

The document scanner requires a physical camera and device storage — it cannot be tested in CI or emulators (ML Kit document scanner doesn't work on emulators). A dedicated test component would make verification easier for contributors and users.

## Acceptance Criteria

- [ ] Livewire debug component with test scenarios
- [ ] Scenario 1: Scan single page as JPEG
- [ ] Scenario 2: Scan multi-page as PDF
- [ ] Scenario 3: Scan with custom quality (50%)
- [ ] Scenario 4: Cancel scan (verify ScanCancelled event)
- [ ] Scenario 5: Gallery import (Android, Epic 1)
- [ ] Event log with all received events
- [ ] Copy log button for sharing results
- [ ] Shows scanned file paths and sizes
- [ ] Step-by-step instructions for each scenario
- [ ] Can be used in the daily-habits app or any NativePHP app

## Implementation Steps

### Step 1: Create Livewire Component

Create `ScannerDebug.php` Livewire component with:

```php
class ScannerDebug extends Component
{
    public array $eventLog = [];
    public array $scannedFiles = [];

    public function scanJpeg(): void
    {
        DocumentScanner::scan(['outputFormat' => 'jpeg', 'maxPages' => 1]);
        $this->log('Scan started', 'JPEG, 1 page');
    }

    public function scanPdf(): void
    {
        DocumentScanner::scan(['outputFormat' => 'pdf']);
        $this->log('Scan started', 'PDF, unlimited pages');
    }

    public function scanLowQuality(): void
    {
        DocumentScanner::scan(['jpegQuality' => 50]);
        $this->log('Scan started', 'JPEG, quality 50');
    }

    #[OnNative(DocumentScanned::class)]
    public function onScanned(mixed ...$data): void { /* log */ }

    #[OnNative(ScanCancelled::class)]
    public function onCancelled(): void { /* log */ }

    #[OnNative(ScanFailed::class)]
    public function onFailed(mixed ...$data): void { /* log */ }
}
```

### Step 2: Create Blade View

- Dark theme matching the app style
- Numbered test scenario buttons with instructions
- Event log with color-coded entries
- Copy log button
- File list showing paths and sizes after scan

### Step 3: Add Route

Add to test app:
```php
Route::get('/scanner-debug', ScannerDebug::class);
```

### Step 4: Document Testing Steps

Each scenario should have:
1. What to tap
2. What to do in the scanner
3. What to check in the event log
4. Pass/fail criteria
