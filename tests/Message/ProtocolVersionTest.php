<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\Message;

use HNV\Http\Message\{
    Message,
    Rules\HttpProtocolVersion as HttpProtocolVersionRules
};
use HNV\Http\MessageTests\DataProvider\ProtocolVersion as ProtocolVersionDataProvider;
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
    #[Attributes\DataProviderExternal(
        className : ProtocolVersionDataProvider::class,
        methodName: 'normalizedSingleValue'
    )]
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
    #[Attributes\DataProviderExternal(
        className : ProtocolVersionDataProvider::class,
        methodName: 'normalizedValues'
    )]
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
    #[Attributes\DataProviderExternal(
        className : ProtocolVersionDataProvider::class,
        methodName: 'normalizedSingleValue'
    )]
    public function getProtocolVersionAfterClear(string $value): void
    {
        $valueCaught = (new Message())
            ->withProtocolVersion($value)
            ->withProtocolVersion('')
            ->getProtocolVersion();

        static::assertSame(HttpProtocolVersionRules::DEFAULT, $valueCaught);
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : ProtocolVersionDataProvider::class,
        methodName: 'invalidValues'
    )]
    public function withProtocolVersionWithInvalidValue(string $value): void
    {
        $valueCaught = (new Message())
            ->withProtocolVersion($value)
            ->getProtocolVersion();

        static::assertSame(HttpProtocolVersionRules::DEFAULT, $valueCaught);
    }
}
