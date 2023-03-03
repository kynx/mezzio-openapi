<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Kynx\Mezzio\OpenApi\Middleware\OperationAddressResolverInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use League\OpenAPIValidation\PSR7\OperationAddress;

use function implode;

trait ValidationMiddlewareTrait
{
    protected function getOpenApi(): OpenApi
    {
        $openApi = Reader::readFromYamlFile(__DIR__ . '/Asset/validation.yaml');
        if (! $openApi->validate()) {
            self::fail(implode("\n", $openApi->getErrors()));
        }

        return $openApi;
    }

    protected function getResolver(): OperationAddressResolverInterface
    {
        $resolver = $this->createStub(OperationAddressResolverInterface::class);
        $resolver->method('getOperationAddress')
            ->willReturn(new OperationAddress('/pets/{petId}', 'get'));

        return $resolver;
    }

    protected function getRequest(string $path): ServerRequest
    {
        return new ServerRequest([], [], new Uri("https://example.com/$path"));
    }
}
