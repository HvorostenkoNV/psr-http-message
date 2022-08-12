<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\Message;

use HNV\Http\Message\{
    Message,
    Rules\HttpProtocolVersion as HttpProtocolVersionRules
};
use PHPUnit\Framework\{
    Attributes,
    TestCase,
};

use function spl_object_id;

/**
 * @internal
 */
#[Attributes\Large]
class ProtocolVersionTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\DataProvider('dataProviderNormalizedValues')]
    public function withProtocolVersionProvidesNewInstance(string $value): void
    {
        $messageFirst   = new Message();
        $messageSecond  = $messageFirst->withProtocolVersion($value);
        $messageThird   = $messageFirst->withProtocolVersion($value);

        static::assertNotSame(
            spl_object_id($messageFirst),
            spl_object_id($messageSecond),
            'Expects instance not the same'
        );
        static::assertNotSame(
            spl_object_id($messageSecond),
            spl_object_id($messageThird),
            'Expects instance not the same'
        );
    }

    #[Attributes\Test]
    #[Attributes\DataProvider('dataProviderNormalizedValues')]
    public function getProtocolVersion(string $value, string $valueNormalized): void
    {
        $valueCaught = (new Message())->withProtocolVersion($value)->getProtocolVersion();

        static::assertSame($valueNormalized, $valueCaught);
    }

    #[Attributes\Test]
    public function getProtocolVersionOnEmptyObject(): void
    {
        $valueCaught = (new Message())->getProtocolVersion();

        static::assertSame(HttpProtocolVersionRules::DEFAULT, $valueCaught);
    }

    #[Attributes\Test]
    #[Attributes\DataProvider('dataProviderNormalizedValues')]
    public function getProtocolVersionAfterClear(string $value): void
    {
        $valueCaught = (new Message())
            ->withProtocolVersion($value)
            ->withProtocolVersion('')
            ->getProtocolVersion();

        static::assertSame(HttpProtocolVersionRules::DEFAULT, $valueCaught);
    }

    #[Attributes\Test]
    #[Attributes\DataProvider('dataProviderInvalidValues')]
    public function withProtocolVersionWithInvalidValue(string $value): void
    {
        $valueCaught = (new Message())
            ->withProtocolVersion($value)
            ->getProtocolVersion();

        static::assertSame(HttpProtocolVersionRules::DEFAULT, $valueCaught);
    }

    public function dataProviderNormalizedValues(): iterable
    {
        yield from $this->normalizedValuesWithOneSegment();
        yield from $this->normalizedValuesWithTwoSegment();
    }

    public function dataProviderInvalidValues(): iterable
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

    private function normalizedValuesWithTwoSegment(): iterable
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
