<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\DataProvider\Header;

use HNV\Http\Helper\Collection\SpecialCharacters;
use HNV\Http\Message\Rules\HeaderValue as HeaderValueRules;

use function array_map;
use function implode;

class HeaderValueGenerator
{
    public static function getNormalizedValues(): iterable
    {
        foreach (self::getValidValues() as $value) {
            $valueString = (string) $value;

            yield $value        => $valueString;
            yield " {$value}"   => $valueString;
            yield "{$value} "   => $valueString;
            yield " {$value} "  => $valueString;
        }
    }

    public static function getInvalidValues(): iterable
    {
        $notAllowedChars = array_map(
            fn (SpecialCharacters $character): string => $character->value,
            HeaderValueRules::ALLOWED_CHARACTERS
        );

        foreach ($notAllowedChars as $char) {
            yield $char;
            yield "{$char}string";
            yield "string{$char}";
            yield "string{$char}string";
        }

        yield implode('', $notAllowedChars);
        yield implode('x', $notAllowedChars);
    }

    /**
     * @return string[]
     */
    private static function getValidValues(): iterable
    {
        $allowedChars = array_map(
            fn (SpecialCharacters $character): string => $character->value,
            HeaderValueRules::ALLOWED_CHARACTERS
        );

        yield 'x';
        yield 'X';

        yield 'string';
        yield 'STRING';
        yield 'String';
        yield 'StRiNg';

        yield 0;
        yield 1;
        yield 123;

        yield '123string';
        yield 'string123';
        yield '123string123';

        yield 'string string';
        yield 'string string string';
        yield 'string 123';
        yield '123 string';
        yield 'string 123 string';

        foreach ($allowedChars as $char) {
            yield $char;
            yield "{$char}string";
            yield "string{$char}";
            yield "string{$char}string";
        }

        yield implode('', $allowedChars);
        yield implode('x', $allowedChars);
    }
}
