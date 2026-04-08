# Epic 3: iOS Max Pages Enforcement

**Priority:** Low
**Status:** Not Started

## Goal

Enforce the `maxPages` limit on iOS. Currently, VisionKit's `VNDocumentCameraViewController` does not have a native page limit API, so users can scan unlimited pages regardless of the `maxPages` setting.

## Background

- **Android**: ML Kit respects `setPageLimit()` natively
- **iOS**: `VNDocumentCameraViewController` has no page limit property. The delegate receives `VNDocumentCameraScan` with all scanned pages after the user finishes.

Two approaches:
1. **Post-scan truncation**: Accept all pages but only save/return up to `maxPages`
2. **Auto-dismiss**: Track page count during scanning and dismiss the scanner when limit is reached (requires monitoring delegate callbacks — VisionKit doesn't provide per-page callbacks, only final result)

Approach 1 (post-scan truncation) is the practical choice since VisionKit doesn't expose per-page events.

## Acceptance Criteria

- [ ] iOS truncates scanned pages to `maxPages` when set (> 0)
- [ ] `pageCount` in `DocumentScanned` event reflects truncated count
- [ ] Only truncated pages are saved to disk (no wasted storage)
- [ ] Config `max_pages_limit` is respected as absolute cap
- [ ] Android behavior unchanged
- [ ] Tests verify `maxPages` is passed to bridge

## Implementation Steps

### Step 1: Pass maxPages to iOS Delegate

1. Add `maxPages` to `DocumentScannerDelegate.configure()`:
   ```swift
   private var maxPages: Int = 0

   func configure(outputFormat: String, jpegQuality: Int, storageDir: String, maxPages: Int) {
       // ...
       self.maxPages = maxPages
   }
   ```

2. In `Scan.execute()`, pass `maxPages`:
   ```swift
   let maxPagesLimit = config["max_pages_limit"] as? Int ?? 100
   let effectiveMaxPages = maxPages > 0 ? min(maxPages, maxPagesLimit) : 0

   DocumentScannerDelegate.shared.configure(
       outputFormat: outputFormat,
       jpegQuality: jpegQuality,
       storageDir: storageDir,
       maxPages: effectiveMaxPages
   )
   ```

### Step 2: Truncate in Delegate

1. In `documentCameraViewController(_:didFinishWith:)`, apply limit:
   ```swift
   let pageCount = maxPages > 0 ? min(scan.pageCount, maxPages) : scan.pageCount

   // Use pageCount instead of scan.pageCount in loops
   for i in 0..<pageCount {
       // ...
   }
   ```

### Step 3: Tests & Docs

1. Add test verifying `maxPages` is passed through bridge call
2. Update docs noting iOS truncation behavior vs Android native limit
