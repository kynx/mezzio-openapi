<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\ContentTypeNegotiator;
use Kynx\Mezzio\OpenApi\Operation\Exception\InvalidContentTypeException;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function fclose;
use function fopen;
use function is_resource;

/**
 * @uses \Kynx\Mezzio\OpenApi\Operation\Exception\InvalidContentTypeException
 *
 * @covers \Kynx\Mezzio\OpenApi\Operation\ContentTypeNegotiator
 */
final class ContentTypeNegotiatorTest extends TestCase
{
    /** @var resource|closed-resource|null */
    private $stream;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stream = null;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * @dataProvider matchedContentTypeProvider
     */
    public function testMatchContentTypeReturnsMatched(string $mimeType, string $expected): void
    {
        $negotiator = new ContentTypeNegotiator([
            '*/*',
            'text/*',
            'text/csv',
            'application/zip',
            'application/geo+json',
        ]);
        $request    = $this->getRequest(['Content-Type' => $mimeType]);

        $actual = $negotiator->negotiate($request);
        self::assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function matchedContentTypeProvider(): array
    {
        return [
            'application/geo+json'   => ['application/geo+json', 'application/geo+json'],
            'application/json'       => ['application/json', '*/*'],
            'application/epub+zip'   => ['application/epub+zip', 'application/zip'],
            ' text/csv '             => [' text/csv ', 'text/csv'],
            'text/csv; charset=utf8' => ['text/csv; charset=utf8', 'text/csv'],
            'text/plain'             => ['text/plain', 'text/*'],
            'application/pdf'        => ['application/pdf', '*/*'],
        ];
    }

    public function testGetMatchedContentTypeThrowsException(): void
    {
        $negotiator = new ContentTypeNegotiator(['image/png']);
        $request    = $this->getRequest(['Content-Type' => 'text/csv']);

        self::expectException(InvalidContentTypeException::class);
        self::expectExceptionMessage("Invalid Content-Type header 'text/csv'; expected one of 'image/png'");
        $negotiator->negotiate($request);
    }

    public function testGetMimeTypesReturnsMimeTypes(): void
    {
        $expected   = ['application/json', 'application/xml'];
        $negotiator = new ContentTypeNegotiator($expected);

        $actual = $negotiator->getMimeTypes();
        self::assertSame($expected, $actual);
    }

    private function getRequest(array $headers): ServerRequestInterface
    {
        $stream = fopen('php://memory', 'r+');
        assert(is_resource($stream));
        $this->stream = $stream;
        return new ServerRequest([], [], null, null, $this->stream, $headers);
    }
}
