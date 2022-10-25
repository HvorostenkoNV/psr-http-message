<?php

declare(strict_types=1);

namespace HNV\Http\Message\Normalizer;

use HNV\Http\Helper\Normalizer\{
    NormalizerInterface,
    NormalizingException,
};
use HNV\Http\Message\Rules\HttpProtocolVersion as HttpProtocolVersionRules;

use function count;
use function explode;
use function implode;
use function is_numeric;

class HttpProtocolVersion implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public static function normalize($value): string
    {
        $delimiter          = HttpProtocolVersionRules::DELIMITER->value;
        $valueSplit         = explode($delimiter, (string) $value);
        $maxPartsCount      = HttpProtocolVersionRules::MAX_PARTS_COUNT;
        $hasOnlyZeroParts   = true;

        if (count($valueSplit) > $maxPartsCount) {
            throw new NormalizingException('protocol version can contains '
                ."maximum [{$maxPartsCount}] parts");
        }

        foreach ($valueSplit as $subValue) {
            if (!is_numeric($subValue) || $subValue < 0) {
                throw new NormalizingException('protocol version can contains '
                    .'only positive numbers or zero');
            }

            if ($subValue > 0) {
                $hasOnlyZeroParts = false;
            }
        }

        if ($hasOnlyZeroParts) {
            throw new NormalizingException('protocol version has only zero parts');
        }

        while (count($valueSplit) < $maxPartsCount) {
            $valueSplit[] = 0;
        }

        return implode($delimiter, $valueSplit);
    }
}
