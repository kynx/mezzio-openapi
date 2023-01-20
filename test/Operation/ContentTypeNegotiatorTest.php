<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\ContentTypeNegotiator;
use Kynx\Mezzio\OpenApi\Operation\OperationException;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

use function fclose;
use function fopen;
use function is_resource;

/**
 * @uses \Kynx\Mezzio\OpenApi\Operation\OperationException
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
        $matcher = new ContentTypeNegotiator([
            '*/*',
            'text/*',
            'text/csv',
            'application/zip',
            'application/geo+json',
        ]);
        $request = $this->getRequest(['Content-Type' => $mimeType]);

        $actual = $matcher->negotiate($request);
        self::assertSame($expected, $actual);
    }

    public function matchedContentTypeProvider(): array
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
        $matcher = new ContentTypeNegotiator(['image/png']);
        $request = $this->getRequest(['Content-Type' => 'text/csv']);

        self::expectException(OperationException::class);
        self::expectExceptionMessage("Invalid Content-Type header 'text/csv'; expected one of 'image/png'");
        $matcher->negotiate($request);
    }

    private function getRequest(array $headers): ServerRequestInterface
    {
        $this->stream = fopen('php://memory', 'r+');
        return new ServerRequest([], [], null, null, $this->stream, $headers);
    }
}
