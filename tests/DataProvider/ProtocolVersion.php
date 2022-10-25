<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\DataProvider;

class ProtocolVersion
{
    public function normalizedValues(): iterable
    {
        yield from $this->normalizedValuesWithOneSegment();
        yield from $this->normalizedValuesWithSeveralSegments();
    }

    public function normalizedSingleValue(): iterable
    {
        foreach ($this->normalizedValues() as $values) {
            yield $values;
            break;
        }
    }

    public function invalidValues(): iterable
    {
        yield [''];
        yield ['0'];
        yield ['0.0'];
        yield ['0.0.0'];
        yield ['0.0.0.0'];

        yield ['x'];
        yield ['x.0'];
        yield ['0.x'];
        yield ['1.x'];

        yield from $this->invalidValuesWithNegativeSegment();
        yield from $this->invalidValuesWithTooManySegments();
    }

    private function normalizedValuesWithOneSegment(): iterable
    {
        for ($number = 1; $number <= 20; $number++) {
            yield ["$number",   "$number.0"];
            yield ["$number.0", "$number.0"];
            yield ["0.$number", "0.$number"];
        }
    }

    private function normalizedValuesWithSeveralSegments(): iterable
    {
        for ($firstNumber = 0; $firstNumber <= 20; $firstNumber++) {
            for ($secondNumber = 1; $secondNumber <= 20; $secondNumber++) {
                yield ["$firstNumber.$secondNumber", "$firstNumber.$secondNumber"];
            }
        }
    }

    private function invalidValuesWithNegativeSegment(): iterable
    {
        for ($number = 1; $number <= 20; $number++) {
            yield ["-$number"];
            yield ["0.-$number"];
            yield ["1.-$number"];
        }
    }

    private function invalidValuesWithTooManySegments(): iterable
    {
        for ($firstNumber = 0; $firstNumber <= 3; $firstNumber++) {
            for ($secondNumber = 1; $secondNumber <= 3; $secondNumber++) {
                for ($thirdNumber = 0; $thirdNumber <= 3; $thirdNumber++) {
                    yield ["$firstNumber.$secondNumber.$thirdNumber"];

                    for ($fourthNumber = 0; $fourthNumber <= 3; $fourthNumber++) {
                        yield ["$firstNumber.$secondNumber.$thirdNumber.$fourthNumber"];
                    }
                }
            }
        }
    }
}
