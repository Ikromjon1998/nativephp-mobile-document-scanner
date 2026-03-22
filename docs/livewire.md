# Usage with Livewire

## Basic Scanner Component

Create a Livewire component that opens the scanner and handles results:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;

class Scanner extends Component
{
    public array $scannedPaths = [];
    public ?string $error = null;
    public bool $scanning = false;

    public function scan()
    {
        $this->error = null;
        $this->scanning = true;

        DocumentScanner::scan();
    }

    #[OnNative(DocumentScanned::class)]
    public function onScanned($data)
    {
        $this->scannedPaths = $data['paths'];
        $this->scanning = false;
    }

    #[OnNative(ScanCancelled::class)]
    public function onCancelled()
    {
        $this->scanning = false;
    }

    #[OnNative(ScanFailed::class)]
    public function onFailed($data)
    {
        $this->error = $data['error'];
        $this->scanning = false;
    }

    public function render()
    {
        return view('livewire.scanner');
    }
}
```

**Blade template** (`resources/views/livewire/scanner.blade.php`):

```blade
<div>
    <button wire:click="scan" :disabled="$scanning">
        {{ $scanning ? 'Scanning...' : 'Scan Document' }}
    </button>

    @if ($error)
        <p style="color: red;">{{ $error }}</p>
    @endif

    @foreach ($scannedPaths as $path)
        <p>{{ $path }}</p>
    @endforeach
</div>
```

## Scan with Options

### Using an Array

```php
public function scanIdCard()
{
    DocumentScanner::scan([
        'maxPages' => 1,
        'outputFormat' => 'jpeg',
        'jpegQuality' => 95,
    ]);
}
```

### Using the ScanOptions DTO

```php
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;

public function scanToPdf()
{
    DocumentScanner::scan(new ScanOptions(
        maxPages: 10,
        outputFormat: OutputFormat::Pdf,
    ));
}
```

## Event Payloads

### DocumentScanned

```php
#[OnNative(DocumentScanned::class)]
public function onScanned($data)
{
    $data['paths'];        // ['/.../scan_1710806400000_0.jpg', '/.../scan_1710806400000_1.jpg']
    $data['pageCount'];    // 2
    $data['outputFormat']; // 'jpeg'
}
```

When output format is `pdf`, `paths` contains a single PDF file:

```php
$data['paths'];        // ['/.../scan_1710806400000.pdf']
$data['pageCount'];    // 5
$data['outputFormat']; // 'pdf'
```

### ScanCancelled

```php
#[OnNative(ScanCancelled::class)]
public function onCancelled()
{
    // No payload — user dismissed the scanner
}
```

### ScanFailed

```php
#[OnNative(ScanFailed::class)]
public function onFailed($data)
{
    $data['error']; // 'Camera access denied'
}
```

## Example: Multi-Page Document with Preview

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;

class DocumentUploader extends Component
{
    public array $documents = [];
    public ?string $error = null;
    public int $totalPages = 0;

    public function scanPages()
    {
        $this->error = null;

        DocumentScanner::scan([
            'maxPages' => 20,
            'outputFormat' => 'jpeg',
            'jpegQuality' => 85,
        ]);
    }

    public function scanAsPdf()
    {
        $this->error = null;

        DocumentScanner::scan([
            'outputFormat' => 'pdf',
        ]);
    }

    #[OnNative(DocumentScanned::class)]
    public function onScanned($data)
    {
        $this->documents = array_merge($this->documents, $data['paths']);
        $this->totalPages += $data['pageCount'];
        $this->error = null;
    }

    #[OnNative(ScanCancelled::class)]
    public function onCancelled()
    {
        // Nothing to do
    }

    #[OnNative(ScanFailed::class)]
    public function onFailed($data)
    {
        $this->error = $data['error'];
    }

    public function removeDocument(int $index)
    {
        unset($this->documents[$index]);
        $this->documents = array_values($this->documents);
    }

    public function clearAll()
    {
        $this->documents = [];
        $this->totalPages = 0;
    }

    public function render()
    {
        return view('livewire.document-uploader');
    }
}
```

## Example: Single-Page ID Card Scanner

```php
public function scanIdCard()
{
    DocumentScanner::scan([
        'maxPages' => 1,
        'outputFormat' => 'jpeg',
        'jpegQuality' => 95,
    ]);
}

#[OnNative(DocumentScanned::class)]
public function onIdScanned($data)
{
    // Single page — take the first (and only) path
    $this->idCardPath = $data['paths'][0];
}
```
