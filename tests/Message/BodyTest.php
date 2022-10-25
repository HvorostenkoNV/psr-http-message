<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\Message;

use InvalidArgumentException;
use HNV\Http\Message\Message;
use HNV\Http\MessageTests\DataProvider\Body as BodyDataProvider;
use PHPUnit\Framework\{
    Attributes,
    TestCase,
};
use Psr\Http\Message\StreamInterface;

use function spl_object_id;

/**
 * @internal
 */
#[Attributes\Small]
class BodyTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : BodyDataProvider::class,
        methodName: 'validValues'
    )]
    public function withBodyProvidesNewInstance(StreamInterface $body): void
    {
        $messageFirst   = new Message();
        $messageSecond  = $messageFirst->withBody($body);
        $messageThird   = $messageFirst->withBody($body);

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
        className : BodyDataProvider::class,
        methodName: 'validValues'
    )]
    public function getBody(StreamInterface $body): void
    {
        $bodyCaught = (new Message())->withBody($body)->getBody();

        static::assertSame((string) $body, (string) $bodyCaught);
    }

    #[Attributes\Test]
    public function getBodyOnEmptyObject(): void
    {
        $body = (new Message())->getBody();

        static::assertSame('', (string) $body);
    }

    #[Attributes\Test]
    #[Attributes\DataProviderExternal(
        className : BodyDataProvider::class,
        methodName: 'invalidValues'
    )]
    public function withBodyThrowsException(StreamInterface $body): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Message())->withBody($body);

        static::fail('expects exception with invalid body');
    }
}
