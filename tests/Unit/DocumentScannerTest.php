<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\DocumentScanner;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;

beforeEach(function (): void {
    $this->scanner = new DocumentScanner;
});

describe('scan', function (): void {
    it('calls the bridge with correct function name and options', function (): void {
        $capturedFunction = null;
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedFunction, &$capturedData) {
            $capturedFunction = $function;
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $result = $this->scanner->scan([
            'maxPages' => 5,
            'outputFormat' => 'jpeg',
            'jpegQuality' => 85,
        ]);

        unset($capturedData['_config']);
        expect($capturedFunction)->toBe('DocumentScanner.Scan')
            ->and($capturedData)->toBe([
                'maxPages' => 5,
                'outputFormat' => 'jpeg',
                'jpegQuality' => 85,
            ])
            ->and($result)->toBe(['success' => true]);
    });

    it('accepts ScanOptions DTO', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan(new ScanOptions(
            maxPages: 3,
            outputFormat: OutputFormat::Pdf,
        ));

        unset($capturedData['_config']);
        expect($capturedData)->toBe([
            'maxPages' => 3,
            'outputFormat' => 'pdf',
        ]);
    });

    it('converts OutputFormat enum to string', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan([
            'outputFormat' => OutputFormat::Pdf,
        ]);

        expect($capturedData['outputFormat'])->toBe('pdf');
    });

    it('passes string outputFormat unchanged', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan([
            'outputFormat' => 'jpeg',
        ]);

        expect($capturedData['outputFormat'])->toBe('jpeg');
    });

    it('handles empty options array', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $result = $this->scanner->scan([]);

        expect($result)->toBe(['success' => true]);
    });

    it('handles scan with no arguments', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $result = $this->scanner->scan();

        expect($result)->toBe(['success' => true]);
    });

    it('returns empty array when bridge returns null', function (): void {
        stubNativephpCallReturnsNull();

        $result = $this->scanner->scan(['maxPages' => 1]);

        expect($result)->toBe([]);
    });

    it('returns empty array when bridge returns invalid json', function (): void {
        stubNativephpCall(fn (): string => 'not-json');

        $result = $this->scanner->scan();

        expect($result)->toBe([]);
    });

    it('injects _config with default values into bridge call', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan();

        expect($capturedData['_config'])->toBe([
            'default_max_pages' => 0,
            'default_output_format' => 'jpeg',
            'default_jpeg_quality' => 90,
            'max_pages_limit' => 100,
            'storage_directory' => 'scanned-documents',
            'default_gallery_import' => false,
            'default_scanner_mode' => 'full',
        ]);
    });

    it('passes galleryImport to bridge call', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan(['galleryImport' => true]);

        expect($capturedData['galleryImport'])->toBeTrue();
    });

    it('passes galleryImport via ScanOptions DTO', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan(new ScanOptions(galleryImport: true));

        unset($capturedData['_config']);
        expect($capturedData)->toHaveKey('galleryImport', true);
    });

    it('throws when outputFormat is invalid', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['outputFormat' => 'bmp']);
    })->throws(InvalidArgumentException::class, 'outputFormat must be "jpeg" or "pdf".');

    it('throws when jpegQuality is below 1', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['jpegQuality' => 0]);
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('throws when jpegQuality is above 100', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['jpegQuality' => 101]);
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('throws when maxPages is negative', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['maxPages' => -1]);
    })->throws(InvalidArgumentException::class, 'maxPages must be 0 (unlimited) or a positive integer.');

    it('throws when galleryImport is not boolean', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['galleryImport' => 'yes']);
    })->throws(InvalidArgumentException::class, 'galleryImport must be a boolean.');

    it('passes scannerMode to bridge call', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan(['scannerMode' => 'base']);

        expect($capturedData['scannerMode'])->toBe('base');
    });

    it('converts ScannerMode enum to string', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        $this->scanner->scan(['scannerMode' => ScannerMode::Filter]);

        expect($capturedData['scannerMode'])->toBe('filter');
    });

    it('throws when scannerMode is invalid', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $this->scanner->scan(['scannerMode' => 'turbo']);
    })->throws(InvalidArgumentException::class, 'scannerMode must be "base", "filter", or "full".');
});
