<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Schema;

use cebe\openapi\spec\OpenApi;

interface CacheInterface
{
    public function get(): ?OpenApi;

    public function set(OpenApi $openApi): void;

    public function clear(): void;
}