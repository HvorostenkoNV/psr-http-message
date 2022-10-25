<?php

declare(strict_types=1);

namespace HNV\Http\Message\Normalizer;

use HNV\Http\Helper\Normalizer\{
    NormalizerInterface,
    NormalizingException,
};
use HNV\Http\Message\Rules\HeaderName as HeaderNameRules;

class HeaderName implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $value
     */
    public static function normalize($value): string
    {
        return $value;
    }
}
