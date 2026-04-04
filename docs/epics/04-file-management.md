# Epic 4: Scanned File Management

**Priority:** Medium
**Status:** Not Started

## Goal

Provide PHP methods to list, retrieve, and delete scanned document files from app storage, so developers can manage storage without manual file path handling.

## Background

Currently, the plugin saves files to a configurable directory but provides no API to manage them afterward. Developers receive file paths via the `DocumentScanned` event but must handle cleanup manually. Over time, scanned files accumulate and consume device storage.

## Acceptance Criteria

- [ ] `DocumentScanner::listFiles()` — returns array of scanned file metadata
- [ ] `DocumentScanner::deleteFile($path)` — deletes a specific scanned file
- [ ] `DocumentScanner::clearAll()` — deletes all scanned files
- [ ] `DocumentScanner::getStorageInfo()` — returns total size and file count
- [ ] Bridge functions implemented on both Android and iOS
- [ ] Path traversal protection (cannot delete files outside storage directory)
- [ ] JS wrappers for all new methods
- [ ] Tests and documentation updated

## Implementation Steps

### Step 1: Define Bridge Functions

Add to `nativephp.json`:
```json
{
    "name": "DocumentScanner.ListFiles",
    "android": "com.nativephp.documentscanner.DocumentScannerFunctions.ListFiles",
    "ios": "DocumentScannerFunctions.ListFiles"
},
{
    "name": "DocumentScanner.DeleteFile",
    "android": "com.nativephp.documentscanner.DocumentScannerFunctions.DeleteFile",
    "ios": "DocumentScannerFunctions.DeleteFile"
},
{
    "name": "DocumentScanner.ClearAll",
    "android": "com.nativephp.documentscanner.DocumentScannerFunctions.ClearAll",
    "ios": "DocumentScannerFunctions.ClearAll"
}
```

### Step 2: PHP Layer

1. Add methods to `DocumentScannerInterface`:
   ```php
   public function listFiles(): array;
   public function deleteFile(string $path): array;
   public function clearAll(): array;
   ```

2. Implement in `DocumentScanner.php`

3. Update facade docblock

### Step 3: Android Native

1. `ListFiles` — list files in storage directory with metadata (name, size, modified date, type)
2. `DeleteFile` — validate path is within storage dir, then delete
3. `ClearAll` — delete all files in storage directory

Security: Validate that resolved path starts with the storage directory (prevent path traversal via `../`).

### Step 4: iOS Native

1. Same three functions using `FileManager`
2. Same path traversal protection

### Step 5: JavaScript

Add `listFiles()`, `deleteFile(path)`, `clearAll()` to `resources/js/index.js`

### Step 6: Tests & Docs

1. Add PHP tests for each new method
2. Update API reference, README
