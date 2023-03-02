<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

final class DelegatingSerializer implements SerializerInterface
{
    /** @var array<array-key, SerializerInterface> */
    private readonly array $delegates;

    public function __construct(SerializerInterface ...$delegates)
    {
        $this->delegates = $delegates;
    }

    public function supports(string $mimeType): bool
    {
        return $this->getDelegate($mimeType) !== null;
    }

    public function serialize(string $mimeType, HydratorInterface|string|null $hydrator, mixed $object): string
    {
        $delegate = $this->getDelegate($mimeType);
        if ($delegate === null) {
            throw SerializerException::unsupportedMimeType($mimeType);
        }

        return $delegate->serialize($mimeType, $hydrator, $object);
    }

    private function getDelegate(string $mimeType): SerializerInterface|null
    {
        foreach ($this->delegates as $delegate) {
            if ($delegate->supports($mimeType)) {
                return $delegate;
            }
        }

        return null;
    }
}
