<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Serializer\DelegatingSerializer;
use Kynx\Mezzio\OpenApi\Serializer\SerializerException;
use Kynx\Mezzio\OpenApi\Serializer\SerializerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DelegatingSerializer::class)]
#[UsesClass(SerializerException::class)]
final class DelegatingSerializerTest extends TestCase
{
    #[DataProvider('supportsProvider')]
    public function testSupportsDelegates(string $mimeType, bool $expected): void
    {
        $unsupported = $this->createStub(SerializerInterface::class);
        $unsupported->method('supports')
            ->willReturn(false);
        $supported = $this->createMock(SerializerInterface::class);
        $supported->method('supports')
            ->with($mimeType)
            ->willReturn($expected);

        $serializer = new DelegatingSerializer($unsupported, $supported);
        $actual     = $serializer->supports($mimeType);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function supportsProvider(): array
    {
        return [
            'unsupported' => ['application/json', false],
            'supported'   => ['text/csv', true],
        ];
    }

    public function testSerializeUnsupportedThrowsException(): void
    {
        $serializer = new DelegatingSerializer();
        $this->expectException(SerializerException::class);
        $this->expectExceptionMessage("Unsupported mime type");
        $serializer->serialize('application/json', null);
    }

    public function testSerializeReturnsSerialized(): void
    {
        $expected = 'SERIALIZED';
        $delegate = $this->createStub(SerializerInterface::class);
        $delegate->method('supports')
            ->willReturn(true);
        $delegate->method('serialize')
            ->willReturn($expected);
        $serializer = new DelegatingSerializer($delegate);

        $actual = $serializer->serialize('text/upper', null);
        self::assertSame($expected, $actual);
    }
}
