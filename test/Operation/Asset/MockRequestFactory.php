<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Asset;

use Kynx\Mezzio\OpenApi\Operation\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MockRequestFactory implements RequestFactoryInterface
{
    public function getOperation(ServerRequestInterface $request): object
    {
        return new MockOperation();
    }
}
