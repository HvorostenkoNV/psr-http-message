<?php

declare(strict_types=1);

namespace HNV\Http\Message;

use HNV\Http\Helper\Normalizer\NormalizingException;
use InvalidArgumentException;
use HNV\Http\Message\{
    Rules\HttpProtocolVersion       as HttpProtocolVersionRules,
    Normalizer\HttpProtocolVersion  as HttpProtocolVersionNormalizer,
};
use HNV\Http\Stream\StreamFactory;
use Psr\Http\Message\{
    MessageInterface,
    StreamInterface,
};

class Message implements MessageInterface
{
    private string          $protocolVersion = HttpProtocolVersionRules::DEFAULT;
    private StreamInterface $body;

    public function __construct()
    {
        $this->body = (new StreamFactory())->createStream();
    }

    public function withProtocolVersion(string $version): static
    {
        $newInstance = clone $this;

        try {
            $newInstance->protocolVersion = HttpProtocolVersionNormalizer::normalize($version);
        } catch (NormalizingException) {
            $newInstance->protocolVersion = HttpProtocolVersionRules::DEFAULT;
        }

        return $newInstance;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withHeader(string $name, array|string $value): static
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader(string $name, array|string $value): static
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader(string $name): static
    {
        // TODO: Implement withoutHeader() method.
    }

    public function hasHeader(string $name): bool
    {
        // TODO: Implement hasHeader() method.
    }

    public function getHeader(string $name): array
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine(string $name): string
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function getHeaders(): array
    {
        // TODO: Implement getHeaders() method.
    }

    public function withBody(StreamInterface $body): static
    {
        if (!$body->isReadable()) {
            throw new InvalidArgumentException('body is not readable');
        }

        $newInstance        = clone $this;
        $newInstance->body  = $body;

        return $newInstance;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }
}
