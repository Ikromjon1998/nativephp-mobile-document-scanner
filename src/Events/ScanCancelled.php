<?php

namespace Ikromjon\DocumentScanner\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanCancelled
{
    use Dispatchable;
    use SerializesModels;
}
