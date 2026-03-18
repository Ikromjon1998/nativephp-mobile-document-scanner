<?php

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentScanned
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
