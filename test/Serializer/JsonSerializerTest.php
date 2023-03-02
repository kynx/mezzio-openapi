<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Hydrator\HydratorInterface;
use Kynx\Mezzio\OpenApi\Serializer\JsonSerializer;
use Kynx\Mezzio\OpenApi\Serializer\SerializerException;
use PHPUnit\Framework\TestCase;
use stdClass;

use const JSON_PRETTY_PRINT;

/**
 * @uses \Kynx\Mezzio\OpenApi\Serializer\SerializerException
 *
 * @covers \Kynx\Mezzio\OpenApi\Serializer\JsonSerializer
 */
final class JsonSerializerTest extends TestCase
{
    private JsonSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new JsonSerializer();
    }

    public function testConstructorAddsThrowOnErrorFlag(): void
    {
        $invalid    = "\xB1\x31";
        $serializer = new JsonSerializer(JSON_PRETTY_PRINT);

        self::expectException(SerializerException::class);
        $serializer->serialize('application/json', null, $invalid);
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupportsReturnsSupported(string $mimeType, bool $expected): void
    {
        $actual = $this->serializer->supports($mimeType);
        self::assertSame($expected, $actual);
    }

    public static function supportsProvider(): array
    {
        return [
            'all'         => ['*/*', true],
            'application' => ['application/*', true],
            'json'        => ['application/json', true],
            'hal+json'    => ['application/hal+json', true],
            'xml'         => ['application/xml', false],
            'empty'       => ['', false],
        ];
    }

    public function testSerializeUnsupportedThrowsException(): void
    {
        self::expectException(SerializerException::class);
        self::expectExceptionMessage("Unsupported mime type");
        $this->serializer->serialize('application/xml', null, new stdClass());
    }

    public function testSerializeNoHydratorReturnsJson(): void
    {
        $expected    = '{"foo":"bar"}';
        $object      = new stdClass();
        $object->foo = "bar";

        $actual = $this->serializer->serialize('application/json', null, $object);
        self::assertSame($expected, $actual);
    }

    public function testSerializeExtractsData(): void
    {
        $expected = '{"foo":"bar"}';
        $hydrator = new class () implements HydratorInterface {
            public static function hydrate(array $data): object
            {
                return (object) $data;
            }

            public static function extract(mixed $object): bool|array|float|int|string|null
            {
                return ['foo' => 'bar'];
            }
        };

        $actual = $this->serializer->serialize('application/json', $hydrator, new stdClass());
        self::assertSame($expected, $actual);
    }
}
