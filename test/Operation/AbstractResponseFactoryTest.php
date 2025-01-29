<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\AbstractResponseFactory;
use Kynx\Mezzio\OpenApi\Operation\Exception\InvalidAcceptException;
use Kynx\Mezzio\OpenApi\Serializer\SerializerInterface;
use KynxTest\Mezzio\OpenApi\Operation\Asset\MockResponseFactory;
use Negotiation\Negotiator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractResponseFactory::class)]
#[UsesClass(InvalidAcceptException::class)]
final class AbstractResponseFactoryTest extends TestCase
{
    private MockResponseFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new MockResponseFactory();
    }

    public function testGetResponseReturnsConfiguredResponse(): void
    {
        $body         = 'Foo';
        $status       = 404;
        $reasonPhrase = 'Missing in action';
        $headers      = ['X-Foo' => 'Bar'];

        $actual = $this->factory->getResponse($body, $status, $reasonPhrase, $headers);
        self::assertSame($body, (string) $actual->getBody());
        self::assertSame($status, $actual->getStatusCode());
        self::assertSame($reasonPhrase, $actual->getReasonPhrase());
        self::assertSame(['X-Foo' => ['Bar']], $actual->getHeaders());
    }

    public function testGetMimeTypeNoPrioritiesThrowsException(): void
    {
        self::expectException(InvalidAcceptException::class);
        self::expectExceptionMessage('Error negotiating Accept header');
        $this->factory->getMimeType(
            new Negotiator(),
            $this->createStub(SerializerInterface::class),
            'application/json',
            []
        );
    }

    public function testGetMimeTypeNoMatchThrowsException(): void
    {
        self::expectException(InvalidAcceptException::class);
        self::expectExceptionMessage("Unsupported mime type 'text/plain'");
        $this->factory->getMimeType(
            new Negotiator(),
            $this->createStub(SerializerInterface::class),
            'text/plain',
            ['application/json']
        );
    }

    public function testGetMimeTypeUnsupportedSerializerThrowsException(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects(self::once())
            ->method('supports')
            ->with('application/xml')
            ->willReturn(false);

        self::expectException(InvalidAcceptException::class);
        self::expectExceptionMessage("Unsupported mime type 'application/xml'");
        $this->factory->getMimeType(
            new Negotiator(),
            $serializer,
            'application/xml; charset=utf-8',
            ['application/json', 'application/xml']
        );
    }

    public function testGetMimeTypeReturnsMatchedType(): void
    {
        $expected   = 'application/json';
        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('supports')
            ->willReturn(true);

        $actual = $this->factory->getMimeType(
            new Negotiator(),
            $serializer,
            'application/json; charset=utf-8',
            ['text/plain', 'application/json']
        );
        self::assertSame($expected, $actual);
    }
}
