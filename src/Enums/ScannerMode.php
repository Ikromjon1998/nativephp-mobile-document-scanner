<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Enums;

enum ScannerMode: string
{
    case Base = 'base';
    case Filter = 'filter';
    case Full = 'full';
}
