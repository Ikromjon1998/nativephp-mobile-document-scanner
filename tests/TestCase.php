<?php

namespace Ikromjon\DocumentScanner\Tests;

use Ikromjon\DocumentScanner\DocumentScannerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DocumentScannerServiceProvider::class,
        ];
    }
}
