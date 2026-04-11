<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Enums;

/**
 * ML Kit scanner processing mode. Android only — iOS always uses full processing.
 */
enum ScannerMode: string
{
    case Base = 'base';
    case Filter = 'filter';
    case Full = 'full';
}
