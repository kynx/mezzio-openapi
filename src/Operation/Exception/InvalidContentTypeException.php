<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;

use function implode;
use function sprintf;

final class InvalidContentTypeException extends DomainException implements ClientExceptionInterface
{
    private function __construct(private string $contentType, private array $expected, string $message)
    {
        parent::__construct($message, 415);
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getExpected(): array
    {
        return $this->expected;
    }

    /**
     * @param list<string> $expected
     */
    public static function fromExpected(string $contentType, array $expected): self
    {
        return new self($contentType, $expected, sprintf(
            "Invalid Content-Type header '%s'; expected one of '%s'",
            $contentType,
            implode(", ", $expected)
        ));
    }
}
