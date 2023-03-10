<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Associates a model class with an OpenApi schema
 *
 * @psalm-immutable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiModel extends AbstractJsonPointerAttribute
{
}
