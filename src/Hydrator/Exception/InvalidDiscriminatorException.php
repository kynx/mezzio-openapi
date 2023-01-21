<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;

use function sprintf;

final class InvalidDiscriminatorException extends DomainException implements ClientExceptionInterface
{
    private function __construct(private string $discriminator, private string $value, string $message)
    {
        parent::__construct($message, 400);
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function fromValue(string $discriminator, string $value): self
    {
        return new self($discriminator, $value, sprintf(
            "Discriminator property '%s' has invalid value '%s'",
            $discriminator,
            $value
        ));
    }
}
