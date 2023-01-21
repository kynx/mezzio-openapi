<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Hydrator\Exception;

use DomainException;
use Kynx\Mezzio\OpenApi\ClientExceptionInterface;

use function sprintf;

final class MissingDiscriminatorException extends DomainException implements ClientExceptionInterface
{
    private function __construct(
        private string $property,
        private string $discriminator,
        string $message
    ) {
        parent::__construct($message, 400);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    public static function fromMissing(string $property, string $discriminator): self
    {
        return new self($property, $discriminator, sprintf(
            "Property '%s' is missing discriminator '%s'",
            $property,
            $discriminator
        ));
    }
}
