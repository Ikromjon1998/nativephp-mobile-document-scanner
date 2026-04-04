<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\DocumentScanner;
use Ikromjon\DocumentScanner\Enums\OutputFormat;

describe('ScanOptions', function (): void {
    it('creates with defaults', function (): void {
        $options = new ScanOptions;

        expect($options->maxPages)->toBe(0)
            ->and($options->outputFormat)->toBe(OutputFormat::Jpeg)
            ->and($options->jpegQuality)->toBe(90);
    });

    it('converts to array with defaults', function (): void {
        $options = new ScanOptions;
        $array = $options->toArray();

        expect($array)->toBe([
            'outputFormat' => 'jpeg',
            'jpegQuality' => 90,
        ]);
    });

    it('includes maxPages when greater than 0', function (): void {
        $options = new ScanOptions(maxPages: 5);
        $array = $options->toArray();

        expect($array['maxPages'])->toBe(5);
    });

    it('excludes maxPages when 0', function (): void {
        $options = new ScanOptions(maxPages: 0);
        $array = $options->toArray();

        expect($array)->not->toHaveKey('maxPages');
    });

    it('converts OutputFormat enum to string', function (): void {
        $options = new ScanOptions(outputFormat: OutputFormat::Pdf);
        $array = $options->toArray();

        expect($array['outputFormat'])->toBe('pdf');
    });

    it('passes string outputFormat unchanged', function (): void {
        $options = new ScanOptions(outputFormat: 'pdf');
        $array = $options->toArray();

        expect($array['outputFormat'])->toBe('pdf');
    });

    it('includes jpegQuality', function (): void {
        $options = new ScanOptions(jpegQuality: 75);
        $array = $options->toArray();

        expect($array['jpegQuality'])->toBe(75);
    });

    it('throws when jpegQuality is below 1', function (): void {
        $options = new ScanOptions(jpegQuality: 0);
        $options->toArray();
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('throws when jpegQuality is above 100', function (): void {
        $options = new ScanOptions(jpegQuality: 101);
        $options->toArray();
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('throws when outputFormat is invalid string', function (): void {
        $options = new ScanOptions(outputFormat: 'tiff');
        $options->toArray();
    })->throws(InvalidArgumentException::class, 'outputFormat must be "jpeg" or "pdf".');

    it('can be passed to scan method', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $scanner = new DocumentScanner;
        $result = $scanner->scan(new ScanOptions(maxPages: 1));

        expect($result)->toBe(['success' => true]);
    });
});
