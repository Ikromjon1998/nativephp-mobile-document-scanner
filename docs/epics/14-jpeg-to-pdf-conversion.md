# Epic 14: JPEG-to-PDF Conversion

**Priority:** High
**Status:** Done

## Goal

Provide a built-in method to combine scanned JPEG pages into a single PDF on-device, so developers don't need a separate PHP library (e.g. fpdf) to produce PDFs from JPEG scans.

## Background

The plugin supports two output formats: JPEG (one file per page) and PDF (single file from native scanner). In practice, most apps scan as JPEG because it gives page-level control — previews, per-page deletion, reordering. But when it's time to share or export, they need a combined PDF.

Currently, developers must bring their own PDF library (e.g. `setasign/fpdf`) to merge JPEGs into a PDF. This is the approach used in the [Smart Docs](https://github.com/Ikromjon1998/smart-docs) demo app. The plugin should handle this natively since both platforms have built-in PDF rendering APIs.

## Acceptance Criteria

- [x] `DocumentScanner::imagesToPdf(array $paths, ?string $outputPath = null): array`
- [x] Returns `['path' => '/abs/path/to/output.pdf']`
- [x] Uses native APIs (Android: `PdfDocument`, iOS: `UIGraphicsPDFRenderer`)
- [x] Handles portrait and landscape images correctly
- [x] `PdfCreated` event dispatched with the output path
- [x] Bridge function registered in `nativephp.json`
- [x] Works independently of scanning (can convert any JPEG files)
- [x] JS wrapper: `imagesToPdf(paths, outputPath?)`
- [x] Tests and documentation

## Implementation Steps

### Step 1: PHP Layer

1. Add `imagesToPdf()` to `DocumentScannerInterface`
2. Implement in `DocumentScanner` — calls new bridge function
3. Create `PdfCreated` event
4. Add validation: paths must be non-empty, files must exist

### Step 2: Android Native

1. New `DocumentScanner.ImagesToPdf` bridge function
2. Load each JPEG as `Bitmap`
3. Create `PdfDocument`, add one page per image
4. Scale images to fit page while preserving aspect ratio
5. Save to storage directory or specified path

### Step 3: iOS Native

1. New `DocumentScannerFunctions.ImagesToPdf` bridge function
2. Load each JPEG as `UIImage`
3. Use `UIGraphicsPDFRenderer` to create pages
4. Auto-detect portrait/landscape per image
5. Save to documents directory or specified path

### Step 4: JS, Tests, Docs

1. Add `imagesToPdf()` to JS bridge
2. Tests for validation, event dispatch
3. Update API reference and README
