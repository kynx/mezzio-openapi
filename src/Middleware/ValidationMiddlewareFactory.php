<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Middleware;

use cebe\openapi\spec\OpenApi;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\RoutedServerRequestValidator;
use Psr\Container\ContainerInterface;

final class ValidationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ValidationMiddleware
    {
        $config           = (array) ($container->get('config')[ConfigProvider::CONFIG_KEY] ?? []);
        $validateResponse = (bool) ($config[ConfigProvider::VALIDATE_KEY]['response'] ?? true);
        $schema           = $container->get(OpenApi::class);

        return new ValidationMiddleware(
            new MezzioOperationAddressResolver(),
            new RoutedServerRequestValidator($schema),
            $validateResponse ? new ResponseValidator($schema) : null
        );
    }
}
