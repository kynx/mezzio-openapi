<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Indicates class should not be overwritten by generator
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class NotOverwritable
{
}
