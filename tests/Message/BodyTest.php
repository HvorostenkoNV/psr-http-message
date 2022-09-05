<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\Message;

use InvalidArgumentException;
use HNV\Http\{
    Message\Message,
    Stream\StreamFactory,
};
use HNV\Http\Helper\Generator\{
    Text        as TextGenerator,
    File        as FileGenerator,
    Resource    as ResourceGenerator,
};
use HNV\Http\Helper\Collection\Resource\{
    AccessMode,
    AccessModeType,
};
use PHPUnit\Framework\{
    Attributes,
    TestCase,
};
use Psr\Http\Message\StreamInterface;

use function array_merge;
use function spl_object_id;

/**
 * @internal
 */
#[Attributes\Small]
class BodyTest extends TestCase
{
    #[Attributes\Test]
    #[Attributes\DataProvider('dataProviderValidValues')]
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
    #[Attributes\DataProvider('dataProviderValidValues')]
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
    #[Attributes\DataProvider('dataProviderInvalidValues')]
    public function withBodyThrowsException(StreamInterface $body): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Message())->withBody($body);

        static::fail('expects exception with invalid body');
    }

    public function dataProviderValidValues(): iterable
    {
        yield from $this->validStreamsFromContent();
        yield from $this->validStreamsFromFile();
        yield from $this->validStreamsFromResource();
    }

    public function dataProviderInvalidValues(): iterable
    {
        foreach ($this->getInvalidResourceModes() as $mode) {
            $filePath   = (new FileGenerator())->generate();
            $resource   = (new ResourceGenerator($filePath, $mode))->generate();
            $stream     = (new StreamFactory())->createStreamFromResource($resource);

            yield [$stream];
        }
    }

    private function validStreamsFromContent(): iterable
    {
        for ($iterator = 10; $iterator >= 0; $iterator--) {
            $content    = (new TextGenerator())->generate();
            $stream     = (new StreamFactory())->createStream($content);

            yield [$stream];
        }
    }

    private function validStreamsFromFile(): iterable
    {
        foreach ($this->getValidResourceModes() as $mode) {
            $filePath   = (new FileGenerator())->generate();
            $stream     = (new StreamFactory())->createStreamFromFile($filePath, $mode->value);

            yield [$stream];
        }
    }

    private function validStreamsFromResource(): iterable
    {
        foreach ($this->getValidResourceModes() as $mode) {
            $filePath   = (new FileGenerator())->generate();
            $resource   = (new ResourceGenerator($filePath, $mode))->generate();
            $stream     = (new StreamFactory())->createStreamFromResource($resource);

            yield [$stream];
        }
    }

    private function getValidResourceModes(): array
    {
        $modeReadableOnly           = AccessMode::get(
            AccessModeType::READABLE_ONLY,
            AccessModeType::EXPECT_NO_FILE
        );
        $modeReadableAndWritable    = AccessMode::get(
            AccessModeType::READABLE_AND_WRITABLE,
            AccessModeType::EXPECT_NO_FILE
        );

        return array_merge($modeReadableOnly, $modeReadableAndWritable);
    }

    private function getInvalidResourceModes(): array
    {
        return AccessMode::get(
            AccessModeType::WRITABLE_ONLY,
            AccessModeType::EXPECT_NO_FILE
        );
    }
}
