<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Mobile\Events\Contracts\DispatchableFromNative;

class DocumentScanned implements DispatchableFromNative
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $paths
     */
    public function __construct(
        public readonly array $paths,
        public readonly int $pageCount,
        public readonly string $outputFormat,
    ) {}
}
