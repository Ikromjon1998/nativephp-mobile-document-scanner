<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\DocumentScanner;

beforeEach(function (): void {
    $this->scanner = new DocumentScanner;
});

describe('imagesToPdf', function (): void {
    it('calls the bridge with correct function name and paths', function (): void {
        $capturedFunction = null;
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedFunction, &$capturedData) {
            $capturedFunction = $function;
            $capturedData = json_decode($data, true);

            return json_encode(['path' => '/output/combined.pdf']);
        });

        $result = $this->scanner->imagesToPdf(['/path/scan_0.jpg', '/path/scan_1.jpg']);

        unset($capturedData['_config']);
        expect($capturedFunction)->toBe('DocumentScanner.ImagesToPdf')
            ->and($capturedData)->toBe([
                'paths' => ['/path/scan_0.jpg', '/path/scan_1.jpg'],
            ])
            ->and($result)->toBe(['path' => '/output/combined.pdf']);
    });

    it('passes outputPath when provided', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['path' => '/custom/output.pdf']);
        });

        $this->scanner->imagesToPdf(['/path/scan_0.jpg'], '/custom/output.pdf');

        unset($capturedData['_config']);
        expect($capturedData)->toBe([
            'paths' => ['/path/scan_0.jpg'],
            'outputPath' => '/custom/output.pdf',
        ]);
    });

    it('does not include outputPath when null', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['path' => '/output/combined.pdf']);
        });

        $this->scanner->imagesToPdf(['/path/scan_0.jpg']);

        unset($capturedData['_config']);
        expect($capturedData)->not->toHaveKey('outputPath');
    });

    it('re-indexes paths array', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['path' => '/output/combined.pdf']);
        });

        $this->scanner->imagesToPdf([2 => '/path/a.jpg', 5 => '/path/b.jpg']);

        unset($capturedData['_config']);
        expect($capturedData['paths'])->toBe(['/path/a.jpg', '/path/b.jpg']);
    });

    it('throws when paths is empty', function (): void {
        stubNativephpCall(fn () => json_encode(['path' => '/output/combined.pdf']));

        $this->scanner->imagesToPdf([]);
    })->throws(InvalidArgumentException::class, 'paths must be a non-empty array.');

    it('throws when a path is not a string', function (): void {
        stubNativephpCall(fn () => json_encode(['path' => '/output/combined.pdf']));

        $this->scanner->imagesToPdf([123]);
    })->throws(InvalidArgumentException::class, 'Each path must be a string.');

    it('returns empty array when bridge returns null', function (): void {
        stubNativephpCallReturnsNull();

        $result = $this->scanner->imagesToPdf(['/path/scan_0.jpg']);

        expect($result)->toBe([]);
    });

    it('injects _config into bridge call', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['path' => '/output/combined.pdf']);
        });

        $this->scanner->imagesToPdf(['/path/scan_0.jpg']);

        expect($capturedData)->toHaveKey('_config');
        expect($capturedData['_config'])->toHaveKey('storage_directory');
    });
});
