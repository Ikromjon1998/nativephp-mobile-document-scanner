<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\DocumentScanner;

beforeEach(function (): void {
    $this->scanner = new DocumentScanner;
});

describe('pdfToImages', function (): void {
    it('calls the bridge with correct function name and path', function (): void {
        $capturedFunction = null;
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedFunction, &$capturedData) {
            $capturedFunction = $function;
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => ['/output/page_0.jpg', '/output/page_1.jpg']]);
        });

        $result = $this->scanner->pdfToImages('/path/scan.pdf');

        unset($capturedData['_config']);
        expect($capturedFunction)->toBe('DocumentScanner.PdfToImages')
            ->and($capturedData)->toBe([
                'pdfPath' => '/path/scan.pdf',
                'quality' => 80,
            ])
            ->and($result)->toBe(['paths' => ['/output/page_0.jpg', '/output/page_1.jpg']]);
    });

    it('passes custom quality', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf', 50);

        unset($capturedData['_config']);
        expect($capturedData['quality'])->toBe(50);
    });

    it('omits quality when null', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf', null);

        unset($capturedData['_config']);
        expect($capturedData)->not->toHaveKey('quality');
    });

    it('throws when pdfPath is empty', function (): void {
        stubNativephpCall(fn () => json_encode(['paths' => []]));

        $this->scanner->pdfToImages('');
    })->throws(InvalidArgumentException::class, 'pdfPath must be a non-empty string.');

    it('throws when quality is below 1', function (): void {
        stubNativephpCall(fn () => json_encode(['paths' => []]));

        $this->scanner->pdfToImages('/path/scan.pdf', 0);
    })->throws(InvalidArgumentException::class, 'quality must be between 1 and 100.');

    it('throws when quality is above 100', function (): void {
        stubNativephpCall(fn () => json_encode(['paths' => []]));

        $this->scanner->pdfToImages('/path/scan.pdf', 101);
    })->throws(InvalidArgumentException::class, 'quality must be between 1 and 100.');

    it('accepts quality of 1', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf', 1);

        expect($capturedData['quality'])->toBe(1);
    });

    it('accepts quality of 100', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf', 100);

        expect($capturedData['quality'])->toBe(100);
    });

    it('returns empty array when bridge returns null', function (): void {
        stubNativephpCallReturnsNull();

        $result = $this->scanner->pdfToImages('/path/scan.pdf');

        expect($result)->toBe([]);
    });

    it('injects _config into bridge call', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf');

        expect($capturedData)->toHaveKey('_config');
        expect($capturedData['_config'])->toHaveKey('storage_directory');
    });

    it('returns empty array when bridge returns invalid json', function (): void {
        stubNativephpCall(fn (): string => 'not-json');

        $result = $this->scanner->pdfToImages('/path/scan.pdf');

        expect($result)->toBe([]);
    });

    it('returns empty array when payload cannot be json encoded', function (): void {
        stubNativephpCall(fn () => json_encode(['paths' => []]));

        $scanner = new class extends DocumentScanner
        {
            protected function nativeConfig(): array
            {
                return ['bad' => "\xB1\x31"];
            }
        };

        $result = $scanner->pdfToImages('/path/scan.pdf');

        expect($result)->toBe([]);
    });

    it('throws when quality is negative', function (): void {
        stubNativephpCall(fn () => json_encode(['paths' => []]));

        $this->scanner->pdfToImages('/path/scan.pdf', -1);
    })->throws(InvalidArgumentException::class, 'quality must be between 1 and 100.');

    it('uses default quality of 80', function (): void {
        $capturedData = null;

        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['paths' => []]);
        });

        $this->scanner->pdfToImages('/path/scan.pdf');

        expect($capturedData['quality'])->toBe(80);
    });

    it('throws RuntimeException when bridge returns native error', function (): void {
        stubNativephpCall(fn () => json_encode(['error' => 'PDF file not found']));

        $this->scanner->pdfToImages('/path/scan.pdf');
    })->throws(RuntimeException::class, 'PDF file not found');
});
