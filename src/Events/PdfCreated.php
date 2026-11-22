<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Mobile\Events\Contracts\DispatchableFromNative;

class PdfCreated implements DispatchableFromNative
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $path,
    ) {}
}
