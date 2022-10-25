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

    public function invalidValues(): iterable
    {
        $letter = 'x';

        yield [''];
        yield ['0'];
        yield ['0.0'];
        yield ['0.0.0'];
        yield ['0.0.0.0'];

        yield ["$letter"];
        yield ["$letter.0"];
        yield ["0.$letter"];
        yield ["1.$letter"];

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
        for ($firstNumber = 0; $firstNumber <= 20; $firstNumber++) {
            for ($secondNumber = 1; $secondNumber <= 20; $secondNumber++) {
                for ($thirdNumber = 0; $thirdNumber <= 20; $thirdNumber++) {
                    yield ["$firstNumber.$secondNumber.$thirdNumber"];

                    for ($fourthNumber = 0; $fourthNumber <= 20; $fourthNumber++) {
                        yield ["$firstNumber.$secondNumber.$thirdNumber.$fourthNumber"];
                    }
                }
            }
        }
    }
}
