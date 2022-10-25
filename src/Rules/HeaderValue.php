<?php

declare(strict_types=1);

namespace HNV\Http\Message\Rules;

use HNV\Http\Helper\Collection\SpecialCharacters;

class HeaderValue
{
    public const ALLOWED_CHARACTERS = [
        SpecialCharacters::UNDERSCORE,
        SpecialCharacters::COLON,
        SpecialCharacters::SEMICOLON,
        SpecialCharacters::DOT,
        SpecialCharacters::COMMA,
        SpecialCharacters::BACK_SLASH,
        SpecialCharacters::FORWARD_SLASH,
        SpecialCharacters::DOUBLE_QUOTATION_MARK,
        SpecialCharacters::APOSTROPHE,
        SpecialCharacters::QUESTION_MARK,
        SpecialCharacters::EXCLAMATION_POINT,
        SpecialCharacters::OPEN_PARENTHESIS,
        SpecialCharacters::CLOSE_PARENTHESIS,
        SpecialCharacters::OPEN_BRACE,
        SpecialCharacters::CLOSE_BRACE,
        SpecialCharacters::OPEN_BRACKET,
        SpecialCharacters::CLOSE_BRACKET,
        SpecialCharacters::AMPERSAT,
        SpecialCharacters::LESS_THAN,
        SpecialCharacters::GREATER_THAN,
        SpecialCharacters::EQUAL,
        SpecialCharacters::MINUS,
        SpecialCharacters::PLUS,
        SpecialCharacters::ASTERISK,
        SpecialCharacters::OCTOTHORPE,
        SpecialCharacters::DOLLAR,
        SpecialCharacters::AMPERSAND,
        SpecialCharacters::GRAVE_ACCENT,
        SpecialCharacters::PIPE,
        SpecialCharacters::TILDE,
        SpecialCharacters::CARET,
        SpecialCharacters::PERCENT,
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
        //TODO: whitespace
    }
}
