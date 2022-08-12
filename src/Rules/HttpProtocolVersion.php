<?php

declare(strict_types=1);

namespace HNV\Http\Message\Rules;

use HNV\Http\Helper\Collection\SpecialCharacters;

class HttpProtocolVersion
{
    public const DEFAULT            = '1.1';
    public const DELIMITER          = SpecialCharacters::DOT;
    public const MAX_PARTS_COUNT    = 2;
}
