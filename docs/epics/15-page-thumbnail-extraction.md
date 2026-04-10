# Epic 15: Page Thumbnail Extraction from PDF

**Priority:** Medium
**Status:** Done

## Goal

Extract page thumbnails from a scanned PDF so developers can show previews without needing a separate PDF rendering library.

## Background

When `outputFormat` is `pdf`, the native scanner produces a single PDF file. Unlike JPEG output (where each page is a separate image), there's no way to show page previews in the app UI. This makes the PDF workflow second-class — developers who want both a PDF and page previews have to scan as JPEG first and then convert.

A `pdfToImages()` method would let developers extract thumbnails from any PDF, closing this gap.

## Acceptance Criteria

- [x] `DocumentScanner::pdfToImages(string $pdfPath, ?int $quality = 80): array`
- [x] Returns `['paths' => ['/path/page_0.jpg', ...]]`
- [x] Uses native APIs (Android: `PdfRenderer`, iOS: `PDFDocument`/`CGPDFPage`)
- [x] Configurable output quality
- [x] Bridge function registered in `nativephp.json`
- [x] JS wrapper: `pdfToImages(pdfPath, quality?)`
- [x] Tests and documentation

## Implementation Steps

### Step 1: PHP Layer

1. Add `pdfToImages()` to `DocumentScannerInterface`
2. Implement in `DocumentScanner`
3. Validation: path must exist, must be a PDF

### Step 2: Android Native

1. New `DocumentScanner.PdfToImages` bridge function
2. Use `PdfRenderer` to open the PDF
3. Render each page to `Bitmap` at specified quality
4. Save as JPEG files to storage directory

### Step 3: iOS Native

1. New `DocumentScannerFunctions.PdfToImages` bridge function
2. Use `PDFDocument` to open the PDF
3. Render each `PDFPage` to `UIImage` via `CGContext`
4. Save as JPEG files to documents directory

### Step 4: JS, Tests, Docs

1. Add `pdfToImages()` to JS bridge
2. Tests for validation, output
3. Update API reference and README
