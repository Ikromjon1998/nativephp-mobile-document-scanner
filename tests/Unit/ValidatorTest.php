<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Validation\ScanValidator;

describe('basic validation', function (): void {
    it('passes with valid options', function (): void {
        ScanValidator::validate([
            'maxPages' => 5,
            'outputFormat' => 'jpeg',
            'jpegQuality' => 85,
        ]);

        expect(true)->toBeTrue();
    });

    it('passes with empty array', function (): void {
        ScanValidator::validate([]);

        expect(true)->toBeTrue();
    });
});

describe('maxPages validation', function (): void {
    it('throws when negative', function (): void {
        ScanValidator::validate(['maxPages' => -1]);
    })->throws(InvalidArgumentException::class, 'maxPages must be 0 (unlimited) or a positive integer.');

    it('allows zero', function (): void {
        ScanValidator::validate(['maxPages' => 0]);

        expect(true)->toBeTrue();
    });

    it('allows positive values', function (): void {
        ScanValidator::validate(['maxPages' => 50]);

        expect(true)->toBeTrue();
    });

    it('throws when exceeding default limit of 100', function (): void {
        ScanValidator::validate(['maxPages' => 101]);
    })->throws(InvalidArgumentException::class, 'maxPages must not exceed 100.');

    it('allows exactly the default limit', function (): void {
        ScanValidator::validate(['maxPages' => 100]);

        expect(true)->toBeTrue();
    });

    it('respects custom max_pages_limit from config', function (): void {
        config(['document-scanner.max_pages_limit' => 10]);

        ScanValidator::validate(['maxPages' => 11]);
    })->throws(InvalidArgumentException::class, 'maxPages must not exceed 10.');

    it('allows value at custom max_pages_limit', function (): void {
        config(['document-scanner.max_pages_limit' => 10]);

        ScanValidator::validate(['maxPages' => 10]);

        expect(true)->toBeTrue();
    });
});

describe('outputFormat validation', function (): void {
    it('allows jpeg', function (): void {
        ScanValidator::validate(['outputFormat' => 'jpeg']);

        expect(true)->toBeTrue();
    });

    it('allows pdf', function (): void {
        ScanValidator::validate(['outputFormat' => 'pdf']);

        expect(true)->toBeTrue();
    });

    it('throws for invalid format', function (): void {
        ScanValidator::validate(['outputFormat' => 'png']);
    })->throws(InvalidArgumentException::class, 'outputFormat must be "jpeg" or "pdf".');
});

describe('jpegQuality validation', function (): void {
    it('throws when below 1', function (): void {
        ScanValidator::validate(['jpegQuality' => 0]);
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('throws when above 100', function (): void {
        ScanValidator::validate(['jpegQuality' => 101]);
    })->throws(InvalidArgumentException::class, 'jpegQuality must be between 1 and 100.');

    it('allows minimum value 1', function (): void {
        ScanValidator::validate(['jpegQuality' => 1]);

        expect(true)->toBeTrue();
    });

    it('allows maximum value 100', function (): void {
        ScanValidator::validate(['jpegQuality' => 100]);

        expect(true)->toBeTrue();
    });

    it('allows mid-range values', function (): void {
        ScanValidator::validate(['jpegQuality' => 50]);

        expect(true)->toBeTrue();
    });
});

describe('galleryImport validation', function (): void {
    it('allows true', function (): void {
        ScanValidator::validate(['galleryImport' => true]);

        expect(true)->toBeTrue();
    });

    it('allows false', function (): void {
        ScanValidator::validate(['galleryImport' => false]);

        expect(true)->toBeTrue();
    });

    it('throws for non-boolean string', function (): void {
        ScanValidator::validate(['galleryImport' => 'yes']);
    })->throws(InvalidArgumentException::class, 'galleryImport must be a boolean.');

    it('throws for non-boolean int', function (): void {
        ScanValidator::validate(['galleryImport' => 1]);
    })->throws(InvalidArgumentException::class, 'galleryImport must be a boolean.');
});

describe('scannerMode validation', function (): void {
    it('allows base', function (): void {
        ScanValidator::validate(['scannerMode' => 'base']);

        expect(true)->toBeTrue();
    });

    it('allows filter', function (): void {
        ScanValidator::validate(['scannerMode' => 'filter']);

        expect(true)->toBeTrue();
    });

    it('allows full', function (): void {
        ScanValidator::validate(['scannerMode' => 'full']);

        expect(true)->toBeTrue();
    });

    it('throws for invalid mode', function (): void {
        ScanValidator::validate(['scannerMode' => 'turbo']);
    })->throws(InvalidArgumentException::class, 'scannerMode must be "base", "filter", or "full".');
});
