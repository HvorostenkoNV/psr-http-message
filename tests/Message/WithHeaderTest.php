<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\Message;

use HNV\Http\Message\Message;
use HNV\Http\MessageTests\DataProvider\Header\WithHeader as WithHeaderDataProvider;
use InvalidArgumentException;
use PHPUnit\Framework\{
    Attributes,
    TestCase,
};

use function spl_object_id;

/**
 * @internal
 */
#[Attributes\Large]
class WithHeaderTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'validSingleValue'
    )]
    public function withHeaderProvidesNewInstance(string $name, string|array $value): void
    {
        $messageFirst   = new Message();
        $messageSecond  = $messageFirst->withHeader($name, $value);
        $messageThird   = $messageFirst->withHeader($name, $value);

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
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'validValues'
    )]
    public function getSavedValue(string $name, string|array $value, array $valueExpected): void
    {
        $valueCaught = (new Message())->withHeader($name, $value)->getHeader($name);

        static::assertSame($valueExpected, $valueCaught);
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'validValues'
    )]
    public function withHeaderRewriteValue(
        string $name,
        string|array $value,
        array $valueExpected
    ): void {
        $valueCaught = (new Message())
            ->withHeader($name, 'someValue')
            ->withHeader($name, $value)
            ->getHeader($name);

        static::assertSame($valueExpected, $valueCaught);
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'valuesWithHeadersInDifferentCases'
    )]
    public function withHeaderSavesHeaderCase(array $headers, array $headersExpected): void
    {
        $message = new Message();

        foreach ($headers as $headerData) {
            $message = $message->withHeader($headerData[0], $headerData[1]);
        }

        static::assertSame($headersExpected, $message->getHeaders());
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'validValues'
    )]
    public function withHeaderClearValue(string $name, string|array $value): void
    {
        $expectedEmptyValue1    = (new Message())
            ->withHeader($name, $value)
            ->withHeader($name, [])
            ->getHeader($name);
        $expectedEmptyValue2    = (new Message())
            ->withHeader($name, $value)
            ->withHeader($name, '')
            ->getHeader($name);

        static::assertSame([], $expectedEmptyValue1);
        static::assertSame([], $expectedEmptyValue2);
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'invalidValues'
    )]
    public function withHeaderThrowsException(string $name, string|array $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Message())->withHeader($name, $value);

        static::fail('expects exception with invalid header name or value');
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : WithHeaderDataProvider::class,
        methodName: 'validWithInvalidValues'
    )]
    public function exceptionThrowingDoesntClearsPreviousValue(
        string $name,
        string|array $validValue,
        string|array $invalidValue,
        array $valueExpected
    ): void {
        $message = (new Message())->withHeader($name, $validValue);

        try {
            $message->withHeader($name, $invalidValue);
        } catch (InvalidArgumentException) {
        }

        static::assertSame($valueExpected, $message->getHeader($name));
    }
}
