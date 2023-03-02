<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Serializer;

use JsonException;
use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;

use function array_pop;
use function explode;
use function json_encode;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class JsonSerializer implements SerializerInterface
{
    public const DEFAULT_JSON_FLAGS = JSON_HEX_TAG
        | JSON_HEX_APOS
        | JSON_HEX_AMP
        | JSON_HEX_QUOT
        | JSON_UNESCAPED_SLASHES;

    private readonly int $jsonFlags;

    public function __construct(int $jsonFlags = self::DEFAULT_JSON_FLAGS)
    {
        $this->jsonFlags = $jsonFlags | JSON_THROW_ON_ERROR;
    }

    public function supports(string $mimeType): bool
    {
        $parts = explode('/', $mimeType);
        if (isset($parts[1])) {
            // ie 'application/hal+json'
            $subParts = explode('+', $parts[1]);
            $parts[1] = array_pop($subParts);
        } else {
            $parts[1] = '*';
        }

        return ($parts[0] === 'application' || $parts[0] === '*')
            && ($parts[1] === 'json' || $parts[1] === '*');
    }

    /**
     * @param class-string<HydratorInterface>|HydratorInterface|null $hydrator
     */
    public function serialize(string $mimeType, HydratorInterface|string|null $hydrator, mixed $object): string
    {
        if (! $this->supports($mimeType)) {
            throw SerializerException::unsupportedMimeType($mimeType);
        }

        try {
            if ($hydrator === null) {
                return json_encode($object, $this->jsonFlags);
            }
            return json_encode($hydrator::extract($object), $this->jsonFlags);
        } catch (JsonException $exception) {
            throw SerializerException::fromThrowable($exception);
        }
    }
}
