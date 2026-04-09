<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\DocumentScanner as DocumentScannerClass;
use Ikromjon\DocumentScanner\Facades\DocumentScanner;

it('resolves to the correct class', function (): void {
    $facade = DocumentScanner::getFacadeRoot();

    expect($facade)->toBeInstanceOf(DocumentScannerClass::class);
});

it('proxies scan calls to the underlying class', function (): void {
    stubNativephpCall(fn () => json_encode(['success' => true]));

    $result = DocumentScanner::scan([
        'maxPages' => 3,
        'outputFormat' => 'jpeg',
    ]);

    expect($result)->toBe(['success' => true]);
});

it('proxies scan with no options', function (): void {
    stubNativephpCall(fn () => json_encode(['success' => true]));

    $result = DocumentScanner::scan();

    expect($result)->toBe(['success' => true]);
});
