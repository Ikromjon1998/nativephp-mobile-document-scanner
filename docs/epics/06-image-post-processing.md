# Epic 6: Image Post-Processing

**Priority:** Low
**Status:** Not Started

## Goal

Provide optional post-processing for scanned images: grayscale conversion, contrast enhancement, and rotation. These run on the device after scanning, without requiring a server.

## Background

While native scanners (VisionKit, ML Kit) apply edge detection and perspective correction during scanning, some users need additional processing:
- **Grayscale** — reduce file size, better OCR results
- **Contrast enhancement** — improve readability of faded documents
- **Rotation** — fix orientation when auto-rotation fails

This is a lower priority because most use cases are served by the native scanner's built-in processing.

## Acceptance Criteria

- [ ] New `DocumentScanner.ProcessImage` bridge function
- [ ] PHP method: `DocumentScanner::processImage($path, $options)`
- [ ] Options: `grayscale` (bool), `contrast` (float 0.5-2.0), `rotation` (0/90/180/270)
- [ ] Processed image saved alongside original (or replaces, based on option)
- [ ] `ImageProcessed` event dispatched with new path
- [ ] Both Android and iOS implementations
- [ ] Tests and documentation

## Implementation Steps

### Step 1: PHP Layer

1. Create `ProcessOptions` DTO:
   ```php
   final readonly class ProcessOptions
   {
       public function __construct(
           public bool $grayscale = false,
           public float $contrast = 1.0,
           public int $rotation = 0,
           public bool $replaceOriginal = false,
       ) {}
   }
   ```

2. Add `processImage()` to interface and main class

3. Add `ImageProcessed` event

### Step 2: Android Native

1. Load bitmap from path
2. Apply transforms using `ColorMatrix` (grayscale, contrast)
3. Apply rotation using `Matrix.postRotate()`
4. Save to storage directory

### Step 3: iOS Native

1. Load `UIImage` from path
2. Apply `CIFilter` for grayscale (`CIPhotoEffectMono`) and contrast (`CIColorControls`)
3. Apply rotation via `CGAffineTransform`
4. Save with configured JPEG quality

### Step 4: JS, Tests, Docs

1. Add `processImage()` to JS bridge
2. Add tests for validation (rotation must be 0/90/180/270, contrast range)
3. Update API reference
