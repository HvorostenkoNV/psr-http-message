<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\DataProvider\Header;

use HNV\Http\Helper\Collection\Resource\AccessMode;
use HNV\Http\Helper\Collection\Resource\AccessModeType;
use HNV\Http\Helper\Generator\{
    File        as FileGenerator,
    Resource    as ResourceGenerator,
};

use function array_fill;
use function array_values;
use function strtolower;
use function strtoupper;
use function ucfirst;

class WithHeader
{
    private string $validName               = '';
    private string $invalidName             = '';
    private string $validValue              = '';
    private string $validValueNormalized    = '';
    private string $invalidValue            = '';

    public function __construct()
    {
        foreach (HeaderNameGenerator::getNormalizedValues() as $nameNormalized) {
            $this->validName = $nameNormalized;
            break;
        }
        foreach (HeaderValueGenerator::getNormalizedValues() as $value => $valueNormalized) {
            $this->validValue           = $value;
            $this->validValueNormalized = $valueNormalized;
            break;
        }
        foreach (HeaderNameGenerator::getInvalidValues() as $name) {
            $this->invalidName = $name;
            break;
        }
        foreach (HeaderValueGenerator::getInvalidValues() as $value) {
            $this->invalidValue = $value;
            break;
        }
    }

    public function validValues(): iterable
    {
        yield from $this->validCombinationsWithSingleHeaderValue();
        yield from $this->validCombinationsWithMultipleHeaderValues();
    }

    public function validSingleValue(): iterable
    {
        foreach ($this->validValues() as $values) {
            yield $values;
            break;
        }
    }

    public function invalidValues(): iterable
    {
        yield from $this->invalidCombinationsWithSingleHeaderValue();
        yield from $this->invalidCombinationsWithMultipleHeaderValues();
        yield from $this->combinationsWithValidAndInvalidHeaderValues();
        yield from $this->combinationsWithHeaderValuesInvalidTypes();
    }

    public function validWithInvalidValues(): iterable
    {
        $invalidValues  = (array) HeaderValueGenerator::getInvalidValues();
        $invalidValue   = array_values($invalidValues)[0];

        foreach ($this->validValues() as $data) {
            yield [$data[0], $data[1], $invalidValue, $data[2]];
        }
    }

    public function valuesWithHeadersInDifferentCases(): iterable
    {
        foreach (HeaderNameGenerator::getNormalizedValues() as $name) {
            $nameLowercase              = strtolower($name);
            $nameUppercase              = strtoupper($name);
            $nameFirstLetterUppercase   = ucfirst($nameLowercase);

            yield [
                [
                    [$nameLowercase,            array_fill(0, 1, $this->validValue)],
                    [$nameUppercase,            array_fill(0, 2, $this->validValue)],
                    [$nameFirstLetterUppercase, array_fill(0, 3, $this->validValue)],
                ],
                [
                    $nameFirstLetterUppercase => array_fill(0, 3, $this->validValue),
                ],
            ];
            yield [
                [
                    [$nameFirstLetterUppercase, array_fill(0, 1, $this->validValue)],
                    [$nameLowercase,            array_fill(0, 2, $this->validValue)],
                    [$nameUppercase,            array_fill(0, 3, $this->validValue)],
                ],
                [
                    $nameUppercase => array_fill(0, 3, $this->validValue),
                ],
            ];
            yield [
                [
                    [$nameUppercase,            array_fill(0, 1, $this->validValue)],
                    [$nameFirstLetterUppercase, array_fill(0, 2, $this->validValue)],
                    [$nameLowercase,            array_fill(0, 3, $this->validValue)],
                ],
                [
                    $nameLowercase => array_fill(0, 3, $this->validValue),
                ],
            ];
        }
    }

    private function validCombinationsWithSingleHeaderValue(): iterable
    {
        foreach (HeaderNameGenerator::getNormalizedValues() as $name) {
            yield [$name, $this->validValue, [$this->validValueNormalized]];
        }

        foreach (HeaderValueGenerator::getNormalizedValues() as $value => $valueNormalized) {
            yield [$this->validName, $value, [$valueNormalized]];
        }
    }

    private function validCombinationsWithMultipleHeaderValues(): iterable
    {
        for ($valuesCount = 1; $valuesCount <= 3; $valuesCount++) {
            yield [
                $this->validName,
                array_fill(0, $valuesCount, $this->validValue),
                array_fill(0, $valuesCount, $this->validValueNormalized),
            ];
        }

        foreach ([
            [$this->validValue, null],
            [$this->validValue, ''],

            [null,  $this->validValue],
            ['',    $this->validValue],

            [null,  $this->validValue,  null],
            ['',    $this->validValue,  ''],

            [null, '', $this->validValue, '', null],
        ] as $valuesCombination) {
            yield [
                $this->validName,
                $valuesCombination,
                [$this->validValueNormalized],
            ];
        }

        yield [$this->validName,    [null, ''], []];
        yield [$this->validName,    [],         []];
    }

    private function invalidCombinationsWithSingleHeaderValue(): iterable
    {
        foreach (HeaderValueGenerator::getInvalidValues() as $invalidValue) {
            yield [$this->validName, $invalidValue];
        }

        foreach (HeaderNameGenerator::getInvalidValues() as $invalidName) {
            yield [$invalidName, $this->validValue];
        }

        yield [$this->invalidName, $this->invalidValue];
    }

    private function invalidCombinationsWithMultipleHeaderValues(): iterable
    {
        foreach (HeaderValueGenerator::getInvalidValues() as $invalidValue) {
            for ($valuesCount = 1; $valuesCount <= 3; $valuesCount++) {
                yield [
                    $this->validName,
                    array_fill(0, $valuesCount, $invalidValue),
                ];
            }
        }

        foreach (HeaderNameGenerator::getInvalidValues() as $invalidName) {
            yield [$invalidName, [$this->validValue]];
        }

        yield [$this->invalidName, [$this->invalidValue]];
    }

    private function combinationsWithValidAndInvalidHeaderValues(): iterable
    {
        foreach (HeaderValueGenerator::getNormalizedValues() as $validValue) {
            foreach ([
                [$validValue, $this->invalidValue],
                [$validValue, $validValue, $this->invalidValue],
                [$validValue, $this->invalidValue, $validValue],
            ] as $valuesCombination) {
                yield [$this->validName,    $valuesCombination];
                yield [$this->invalidName,  $valuesCombination];
            }
        }

        foreach (HeaderValueGenerator::getInvalidValues() as $invalidValue) {
            foreach ([
                [$this->validValue, $invalidValue],
                [$this->validValue, $this->validValue, $invalidValue],
                [$this->validValue, $invalidValue, $this->validValue],
            ] as $valuesCombination) {
                yield [$this->validName,    $valuesCombination];
                yield [$this->invalidName,  $valuesCombination];
            }
        }
    }

    private function combinationsWithHeaderValuesInvalidTypes(): iterable
    {
        $resourceMode   = AccessMode::get(
            AccessModeType::READABLE_AND_WRITABLE,
            AccessModeType::EXPECT_NO_FILE
        )[0];
        $filePath       = (new FileGenerator())->generate();
        $resource       = (new ResourceGenerator($filePath, $resourceMode))->generate();

        foreach ([
            (new class() {}),
            $resource,
            true,
            false,
        ] as $valueWithInvalidType) {
            foreach ([
                [$valueWithInvalidType],
                [$this->validValue, $valueWithInvalidType],
                [$this->validValue, $valueWithInvalidType, $this->validValue],
                [$this->validValue, $valueWithInvalidType, $this->invalidValue],
            ] as $valuesCombination) {
                yield [$this->validName, $valuesCombination];
            }
        }
    }
}
