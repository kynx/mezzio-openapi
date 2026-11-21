<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Attribute;

use Attribute;

/**
 * Associates hydrator with OpenAPI schema
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OpenApiHydrator extends AbstractJsonPointerAttribute
{
}
