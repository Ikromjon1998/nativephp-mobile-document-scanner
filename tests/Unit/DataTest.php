<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\DocumentScanner;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;

describe('ScanOptions', function (): void {
    it('creates with defaults', function (): void {
        $options = new ScanOptions;

        expect($options->maxPages)->toBe(0)
            ->and($options->outputFormat)->toBe(OutputFormat::Jpeg)
            ->and($options->jpegQuality)->toBe(90)
            ->and($options->galleryImport)->toBeFalse()
            ->and($options->scannerMode)->toBe(ScannerMode::Full);
    });

    it('converts to array with defaults as empty', function (): void {
        $options = new ScanOptions;
        $array = $options->toArray();

        expect($array)->toBe([]);
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

    it('excludes outputFormat when default jpeg', function (): void {
        $options = new ScanOptions(outputFormat: OutputFormat::Jpeg);
        $array = $options->toArray();

        expect($array)->not->toHaveKey('outputFormat');
    });

    it('includes jpegQuality when non-default', function (): void {
        $options = new ScanOptions(jpegQuality: 75);
        $array = $options->toArray();

        expect($array['jpegQuality'])->toBe(75);
    });

    it('excludes jpegQuality when default 90', function (): void {
        $options = new ScanOptions(jpegQuality: 90);
        $array = $options->toArray();

        expect($array)->not->toHaveKey('jpegQuality');
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

    it('includes galleryImport when true', function (): void {
        $options = new ScanOptions(galleryImport: true);
        $array = $options->toArray();

        expect($array['galleryImport'])->toBeTrue();
    });

    it('excludes galleryImport when false', function (): void {
        $options = new ScanOptions(galleryImport: false);
        $array = $options->toArray();

        expect($array)->not->toHaveKey('galleryImport');
    });

    it('converts ScannerMode enum to string', function (): void {
        $options = new ScanOptions(scannerMode: ScannerMode::Base);
        $array = $options->toArray();

        expect($array['scannerMode'])->toBe('base');
    });

    it('passes string scannerMode unchanged', function (): void {
        $options = new ScanOptions(scannerMode: 'filter');
        $array = $options->toArray();

        expect($array['scannerMode'])->toBe('filter');
    });

    it('excludes scannerMode when default full', function (): void {
        $options = new ScanOptions(scannerMode: ScannerMode::Full);
        $array = $options->toArray();

        expect($array)->not->toHaveKey('scannerMode');
    });

    it('throws when scannerMode is invalid string', function (): void {
        $options = new ScanOptions(scannerMode: 'turbo');
        $options->toArray();
    })->throws(InvalidArgumentException::class, 'scannerMode must be "base", "filter", or "full".');

    it('can be passed to scan method', function (): void {
        stubNativephpCall(fn () => json_encode(['success' => true]));

        $scanner = new DocumentScanner;
        $result = $scanner->scan(new ScanOptions(maxPages: 1));

        expect($result)->toBe(['success' => true]);
    });
});
