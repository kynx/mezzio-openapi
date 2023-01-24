<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation\Asset;

use Kynx\Mezzio\OpenApi\Operation\OperationFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MockOperationFactory implements OperationFactoryInterface
{
    public function getOperation(ServerRequestInterface $request): object
    {
        return new MockOperation();
    }
}
