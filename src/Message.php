<?php

declare(strict_types=1);

namespace HNV\Http\Message;

use HNV\Http\Helper\Normalizer\NormalizingException;
use InvalidArgumentException;
use HNV\Http\Message\{
    Rules\HttpProtocolVersion       as HttpProtocolVersionRules,
    Normalizer\HttpProtocolVersion  as HttpProtocolVersionNormalizer,
    Normalizer\HeaderName           as HeaderNameNormalizer,
    Normalizer\HeaderValue          as HeaderValueNormalizer,
};
use HNV\Http\Stream\StreamFactory;
use Psr\Http\Message\{
    MessageInterface,
    StreamInterface,
};

use function gettype;
use function implode;
use function is_array;
use function strlen;
use function strtolower;

class Message implements MessageInterface
{
    private string          $protocolVersion    = HttpProtocolVersionRules::DEFAULT;
    private StreamInterface $body;
    private array           $headers            = [];
    private array           $headersNamesCases  = [];

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
        $headerNameNormalized   = $this->getHeaderNameNormalized($name);
        $headerNameLowercase    = strtolower($headerNameNormalized);
        $headerValuesNormalized = $this->getHeaderValuesNormalized($value);
        $newInstance            = clone $this;

        $newInstance->headers[$headerNameLowercase]             = $headerValuesNormalized;
        $newInstance->headersNamesCases[$headerNameLowercase]   = $headerNameNormalized;

        return $newInstance;
    }

    public function withAddedHeader(string $name, array|string $value): static
    {
        //TODO
        return $this;
    }

    public function withoutHeader(string $name): static
    {
        //TODO
        return $this;
    }

    public function hasHeader(string $name): bool
    {
        //TODO
        return false;
    }

    public function getHeader(string $name): array
    {
        $headerNameLowercase = strtolower($name);

        return $this->headers[$headerNameLowercase] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function getHeaders(): array
    {
        $result = [];

        foreach ($this->headers as $headerNameLowercase => $values) {
            $headerName     = $this->headersNamesCases[$headerNameLowercase]    ?? null;
            $headerValues   = $this->headers[$headerNameLowercase]              ?? null;

            if ($headerValues) {
                $result[$headerName] = $headerValues;
            }
        }

        return $result;
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

    /**
     * @throws InvalidArgumentException
     */
    private function getHeaderNameNormalized(string $name): string
    {
        try {
            return HeaderNameNormalizer::normalize($name);
        } catch (NormalizingException $exception) {
            throw new InvalidArgumentException("header name [{$name}] is invalid", 0, $exception);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getHeaderValuesNormalized(array|string $value): array
    {
        $headerValues   = is_array($value) ? $value : [$value];
        $result         = [];

        foreach ($headerValues as $headerValue) {
            try {
                $headerValueType = gettype($headerValue);

                switch ($headerValueType) {
                    case 'string':
                        if (strlen($headerValue) > 0) {
                            $result[] = HeaderValueNormalizer::normalize($headerValue);
                        }
                        break;
                    case 'NULL':
                        break;
                    case 'integer':
                    case 'double':
                        $result[] = HeaderValueNormalizer::normalize((string) $headerValue);
                        break;
                    default:
                        throw new NormalizingException('header value type '.
                            "{$headerValueType} is unprocessable");
                }
            } catch (NormalizingException $exception) {
                throw new InvalidArgumentException(
                    "header value [{$headerValue}] is invalid",
                    0,
                    $exception
                );
            }
        }

        return $result;
    }
}
