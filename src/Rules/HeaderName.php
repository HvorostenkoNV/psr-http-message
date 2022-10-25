<?php

declare(strict_types=1);

namespace HNV\Http\Message\Rules;

use HNV\Http\Helper\Collection\SpecialCharacters;

class HeaderName
{
    public const ALLOWED_CHARACTERS = [
        SpecialCharacters::MINUS,
        SpecialCharacters::UNDERSCORE,
    ];

    public static function mask(): string
    {
        $letterLowercase    = 'a-z';
        $letterUppercase    = 'A-Z';
        $specialCharacters  = '';

        foreach (static::ALLOWED_CHARACTERS as $case) {
            $specialCharacters .= "\\{$case->value}";
        }

        return "/^[{$letterLowercase}{$letterUppercase}{$specialCharacters}]{1,}$/";
    }
}
