<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $path,
    ) {}
}
