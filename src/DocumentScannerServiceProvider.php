<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner;

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Illuminate\Support\ServiceProvider;

class DocumentScannerServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/document-scanner.php',
            'document-scanner',
        );

        $this->app->singleton(DocumentScannerInterface::class, fn (): DocumentScanner => new DocumentScanner);
        $this->app->alias(DocumentScannerInterface::class, DocumentScanner::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'document-scanner');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/document-scanner.php' => config_path('document-scanner.php'),
            ], 'document-scanner-config');
        }
    }
}
