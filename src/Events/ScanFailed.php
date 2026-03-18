<?php

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanFailed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $error,
    ) {}
}
