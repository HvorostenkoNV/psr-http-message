<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\DataProvider\Header;

use HNV\Http\Helper\Collection\SpecialCharacters;
use HNV\Http\Message\Rules\HeaderName as HeaderNameRules;

use function array_diff;
use function array_fill;
use function array_map;
use function implode;
use function str_repeat;

class HeaderNameGenerator
{
    public static function getNormalizedValues(): iterable
    {
        foreach (self::getValidValues() as $value) {
            yield $value        => $value;
            yield " {$value}"   => $value;
            yield "{$value} "   => $value;
            yield " {$value} "  => $value;
        }
    }

    public static function getInvalidValues(): iterable
    {
        $allCharacters      = SpecialCharacters::casesValues();
        $allowedChars       = array_map(
            fn (SpecialCharacters $character): string => $character->value,
            HeaderNameRules::ALLOWED_CHARACTERS
        );
        $notAllowedChars    = array_diff($allCharacters, $allowedChars);

        yield '';
        yield 1;
        yield 123;

        foreach ($allowedChars as $char) {
            yield "1{$char}1";

            foreach (self::getValidValues() as $validValue) {
                yield "{$char}{$validValue}";
                yield "{$validValue}{$char}";
                yield "{$char}{$validValue}{$char}";
            }
        }

        foreach ($notAllowedChars as $char) {
            yield "x{$char}x";
        }
    }

    /**
     * @return string[]
     */
    private static function getValidValues(): iterable
    {
        $allowedChars = array_map(
            fn (SpecialCharacters $character): string => $character->value,
            HeaderNameRules::ALLOWED_CHARACTERS
        );

        foreach ([
            'x',
            'X',

            'string',
            'STRING',
            'String',
            'StRiNg',
        ] as $string) {
            $valueWithAllSpecialCharacters = $string;

            yield $string;

            foreach ($allowedChars as $char) {
                $valueWithAllSpecialCharacters .= $char.$string;

                for ($stringRepeat = 2; $stringRepeat <= 5; $stringRepeat++) {
                    for ($specialCherRepeat = 1; $specialCherRepeat <= 5; $specialCherRepeat++) {
                        $delimiter  = str_repeat($char, $specialCherRepeat);
                        $stringsSet = array_fill(0, $stringRepeat, $string);

                        yield implode($delimiter, $stringsSet);
                    }
                }
            }

            yield $valueWithAllSpecialCharacters;
        }
    }
}
