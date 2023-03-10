<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Middleware;

use cebe\openapi\spec\OpenApi;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException;
use Kynx\Mezzio\OpenApi\Middleware\ValidationMiddlewareFactory;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @uses \Kynx\Mezzio\OpenApi\Middleware\Exception\ResponseValidationException
 * @uses \Kynx\Mezzio\OpenApi\Middleware\MezzioOperationAddressResolver
 * @uses \Kynx\Mezzio\OpenApi\Middleware\ValidationMiddleware
 * @uses \Kynx\Mezzio\OpenApi\RouteOptionsUtil
 *
 * @covers \Kynx\Mezzio\OpenApi\Middleware\ValidationMiddlewareFactory
 */
final class ValidationMiddlewareFactoryTest extends TestCase
{
    use MezzioRequestTrait;
    use ValidationMiddlewareTrait;

    public function testInvokeReturnsValidateResponseInstance(): void
    {
        $factory  = new ValidationMiddlewareFactory();
        $instance = $factory($this->getContainer(true));
        $request  = $this->getOperationRequest('/paths/~1pets~1{petId}/get')
            ->withUri(new Uri('https://example.com/pets/123'));

        self::expectException(ResponseValidationException::class);
        $instance->process($request, new MockHandler());
    }

    public function testInvokeReturnsNoValidateResponseInstance(): void
    {
        $factory  = new ValidationMiddlewareFactory();
        $instance = $factory($this->getContainer(false));
        $request  = $this->getOperationRequest('/paths/~1pets~1{petId}/get')
            ->withUri(new Uri('https://example.com/pets/123'));

        $actual = $instance->process($request, new MockHandler());
        self::assertInstanceOf(EmptyResponse::class, $actual);
    }

    private function getContainer(bool $validateResponse): ContainerInterface
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
            ->willReturnMap([
                [
                    'config',
                    [
                        ConfigProvider::CONFIG_KEY => [
                            ConfigProvider::VALIDATE_KEY => [
                                'response' => $validateResponse,
                            ],
                        ],
                    ],
                ],
                [OpenApi::class, $this->getOpenApi()],
            ]);

        return $container;
    }
}
