<?php

use Ikromjon\DocumentScanner\DocumentScanner;

it('registers DocumentScanner as a singleton', function (): void {
    $instance1 = $this->app->make(DocumentScanner::class);
    $instance2 = $this->app->make(DocumentScanner::class);

    expect($instance1)->toBeInstanceOf(DocumentScanner::class)
        ->and($instance1)->toBe($instance2);
});

it('resolves DocumentScanner from the container', function (): void {
    $instance = $this->app->make(DocumentScanner::class);

    expect($instance)->toBeInstanceOf(DocumentScanner::class);
});

it('registers the document-scanner view namespace', function (): void {
    $hints = $this->app['view']->getFinder()->getHints();

    expect($hints)->toHaveKey('document-scanner');
});

it('merges default config values', function (): void {
    expect(config('document-scanner.default_max_pages'))->toBe(0)
        ->and(config('document-scanner.max_pages_limit'))->toBe(100)
        ->and(config('document-scanner.default_output_format'))->toBe('jpeg')
        ->and(config('document-scanner.default_jpeg_quality'))->toBe(90)
        ->and(config('document-scanner.storage_directory'))->toBe('scanned-documents');
});
