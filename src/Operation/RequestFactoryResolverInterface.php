<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Operation;

use Psr\Http\Message\ServerRequestInterface;

interface RequestFactoryResolverInterface
{
    public function getFactory(ServerRequestInterface $request): RequestFactoryInterface|null;
}
