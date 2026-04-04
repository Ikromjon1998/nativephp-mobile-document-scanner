<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;

describe('DocumentScanned', function (): void {
    it('stores paths, pageCount, and outputFormat', function (): void {
        $event = new DocumentScanned(
            paths: ['/path/to/scan_0.jpg', '/path/to/scan_1.jpg'],
            pageCount: 2,
            outputFormat: 'jpeg',
        );

        expect($event->paths)->toBe(['/path/to/scan_0.jpg', '/path/to/scan_1.jpg'])
            ->and($event->pageCount)->toBe(2)
            ->and($event->outputFormat)->toBe('jpeg');
    });

    it('has readonly properties', function (): void {
        $event = new DocumentScanned([], 0, 'jpeg');

        $reflection = new ReflectionClass($event);

        expect($reflection->getProperty('paths')->isReadOnly())->toBeTrue()
            ->and($reflection->getProperty('pageCount')->isReadOnly())->toBeTrue()
            ->and($reflection->getProperty('outputFormat')->isReadOnly())->toBeTrue();
    });

    it('handles pdf output format', function (): void {
        $event = new DocumentScanned(
            paths: ['/path/to/scan.pdf'],
            pageCount: 3,
            outputFormat: 'pdf',
        );

        expect($event->paths)->toHaveCount(1)
            ->and($event->outputFormat)->toBe('pdf');
    });
});

describe('ScanCancelled', function (): void {
    it('can be instantiated with no arguments', function (): void {
        $event = new ScanCancelled;

        expect($event)->toBeInstanceOf(ScanCancelled::class);
    });
});

describe('ScanFailed', function (): void {
    it('stores error message', function (): void {
        $event = new ScanFailed(error: 'Camera access denied');

        expect($event->error)->toBe('Camera access denied');
    });

    it('has readonly error property', function (): void {
        $event = new ScanFailed('error');

        $reflection = new ReflectionClass($event);

        expect($reflection->getProperty('error')->isReadOnly())->toBeTrue();
    });
});
