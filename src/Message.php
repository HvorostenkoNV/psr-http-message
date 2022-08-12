<?php

declare(strict_types=1);

namespace HNV\Http\Message;

use HNV\Http\Helper\Normalizer\NormalizingException;
use HNV\Http\Message\{
    Rules\HttpProtocolVersion       as HttpProtocolVersionRules,
    Normalizer\HttpProtocolVersion  as HttpProtocolVersionNormalizer,
};
use Psr\Http\Message\{
    MessageInterface,
    StreamInterface,
};

class Message implements MessageInterface
{
    private string $protocolVersion = HttpProtocolVersionRules::DEFAULT;

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
        // TODO: Implement withBody() method.
    }

    public function getBody(): StreamInterface
    {
        // TODO: Implement getBody() method.
    }
}
