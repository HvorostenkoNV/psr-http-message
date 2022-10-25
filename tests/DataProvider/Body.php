<?php

declare(strict_types=1);

namespace HNV\Http\MessageTests\DataProvider;

use HNV\Http\Stream\StreamFactory;
use HNV\Http\Helper\Generator\{
    Text        as TextGenerator,
    File        as FileGenerator,
    Resource    as ResourceGenerator,
};
use HNV\Http\Helper\Collection\Resource\{
    AccessMode,
    AccessModeType,
};

use function array_merge;

class Body
{
    public function validValues(): iterable
    {
        yield from $this->validStreamsFromContent();
        yield from $this->validStreamsFromFile();
        yield from $this->validStreamsFromResource();
    }

    public function invalidValues(): iterable
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
