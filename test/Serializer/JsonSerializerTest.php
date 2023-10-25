<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Serializer;

use Kynx\Mezzio\OpenApi\Serializer\JsonSerializer;
use Kynx\Mezzio\OpenApi\Serializer\SerializerException;
use PHPUnit\Framework\TestCase;

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
        $serializer->serialize('application/json', $invalid);
    }

    public function testConstructorSetsOverridesJsonFlags(): void
    {
        $expected   = <<<END_OF_EXPECTED
        {
            "a": "foo",
            "b": "bar"
        }
        END_OF_EXPECTED;
        $data       = ['a' => 'foo', 'b' => 'bar'];
        $serializer = new JsonSerializer(JSON_PRETTY_PRINT);
        $actual     = $serializer->serialize('application/json', $data);
        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupportsReturnsSupported(string $mimeType, bool $expected): void
    {
        $actual = $this->serializer->supports($mimeType);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
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
}
