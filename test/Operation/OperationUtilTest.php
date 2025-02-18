<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Operation;

use Kynx\Mezzio\OpenApi\Operation\OperationUtil;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Rize\UriTemplate;

#[CoversClass(OperationUtil::class)]
final class OperationUtilTest extends TestCase
{
    private UriTemplate $uriTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uriTemplate = new UriTemplate();
    }

    #[DataProvider('pathVariableProvider')]
    public function testGetPathVariablesPopulatesPathParams(string $path, string $template, array $expected): void
    {
        $request = $this->getRequest(new Uri('http://example.com' . $path), [], []);

        $actual = OperationUtil::getPathVariables($this->uriTemplate, $template, $request);
        self::assertEquals($expected, $actual);
    }

    /**
     * Note that it is impossible to distinguish between array and object notation unless explode is used. Parsers
     * will need to account for this and use the schema type to determine how the returned array is represented.
     *
     * @return array<string, array{0: string, 1: string, 2: array}>
     */
    public static function pathVariableProvider(): array
    {
        return [
            'simple_primitive' => ['/users/3', '/users/{id}', ['id' => '3']],
            'simple_array'     => ['/users/3,4', '/users/{id}', ['id' => ['3', '4']]],
            //'simple_object'   => ['/users/a,b,c,d', '/users/{id}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'simple_explode_primitive' => ['/users/3', '/users/{id*}', ['id' => ['3']]],
            'simple_explode_array'     => ['/users/3,4', '/users/{id*}', ['id' => ['3', '4']]],
            'simple_explode_object'    => ['/users/a=b,c=d', '/users/{id*}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'label_primitive'          => ['/users/.5', '/users/{.id}', ['id' => '5']],
            'label_array'              => ['/users/.3.4.5', '/users/{.id}', ['id' => ['3', '4', '5']]],
            //'label_object'   => ['/users/.a.b.c.d', '/users/{.id}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'label_explode_primitive' => ['/users/.3', '/users/{.id*}', ['id' => ['3']]],
            'label_explode_array'     => ['/users/.3.4', '/users/{.id*}', ['id' => ['3', '4']]],
            'label_explode_object'    => ['/users/.a=b.c=d', '/users/{.id*}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'matrix_primitive'        => ['/users/;id=5', '/users/{;id}', ['id' => '5']],
            'matrix_array'            => ['/users/;id=3,4,5', '/users/{;id}', ['id' => ['3', '4', '5']]],
            //'matrix_object'   => ['/users/;id=a,b,c,d', '/users/{;id}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'matrix_explode_primitive' => ['/users/;id=3', '/users/{;id*}', ['id' => ['3']]],
            'matrix_explode_array'     => ['/users/;id=3;id=4', '/users/{;id*}', ['id' => ['3', '4']]],
            'matrix_explode_object'    => ['/users/;a=b;c=d', '/users/{;id*}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'url_encoded'              => ['/users/A%20B', '/users/{id}', ['id' => 'A B']],
        ];
    }

    #[DataProvider('queryVariableProvider')]
    public function testGetQueryVariablesPopulatesQueryParams(string $query, string $template, array $expected): void
    {
        $request = $this->getRequest(new Uri('http://example.com/users/123' . $query), [], []);

        $actual = OperationUtil::getQueryVariables($this->uriTemplate, $template, $request);
        self::assertEquals($expected, $actual);
    }

    /**
     * @fixme space_explode and pipe_explode will require custom handling, similar to `rize/url-template` '%' operator
     * @return array<string, array{0: string, 1: string, 2: array}>
     */
    public static function queryVariableProvider(): array
    {
        return [
            'form_explode_primitive' => ['?id=5', '{?id*}', ['id' => ['5']]],
            'form_explode_array'     => ['?id=3&id=4', '{?id*}', ['id' => ['3', '4']]],
            'form_explode_object'    => ['?a=b&c=d', '{?id*}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'form_explode_empty'     => ['', '{?id*}', ['id' => null]],
            'form_primitive'         => ['?id=5', '{?id}', ['id' => '5']],
            'form_array'             => ['?id=3,4,5', '{?id}', ['id' => ['3', '4', '5']]],
            'space_array'            => ['?id=3&id=4', '{?id*}', ['id' => ['3', '4']]],
            // 'space_explode_array'    => ['?id=3%204%205', '{?id_}', ['id' => ['3', '4', '5']]],
            'pipe_array' => ['?id=3&id=4', '{?id*}', ['id' => ['3', '4']]],
            // 'pipe_explode_array'     => ['?id=3|4|5', '{?id|}', ['id' => ['3', '4', '5']]],
            'deep_explode_object' => ['?id[a]=b&id[c]=d', '{?id%}', ['id' => ['a' => 'b', 'c' => 'd']]],
            'url_encoded'         => ['?id=A+B', '{?id*}', ['id' => ['A B']]],
            'deep_url_encoded'    => ['?id[a]=b%20c', '{?id%}', ['id' => ['a' => 'b c']]],
        ];
    }

    /**
     * @param array<string, string> $templates
     */
    #[DataProvider('headerVariableProvider')]
    public function testGetHeaderVariablesPopulatesHeaderParams(array $headers, array $templates, array $expected): void
    {
        $uri     = new Uri('http://example.com/');
        $request = $this->getRequest($uri, $headers, []);

        $actual = OperationUtil::getHeaderVariables($this->uriTemplate, $templates, $request);
        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{0: array, 1: array<string, string>, 2: array}>
     */
    public static function headerVariableProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'simple_primitive'         => [['X-ID' => '5'], ['X-ID' => '{X-ID}'], ['X-ID' => '5']],
            'simple_array'             => [['X-ID' => '3,4'], ['X-ID' => '{X-ID}'], ['X-ID' => ['3', '4']]],
            'simple_empty'             => [[], ['X-ID' => '{X-ID}'], ['X-ID' => null]],
            'simple_explode_primitive' => [['X-ID' => '5'], ['X-ID' => '{X-ID*}'], ['X-ID' => ['5']]],
            'simple_explode_array'     => [['X-ID' => '3,4'], ['X-ID' => '{X-ID*}'], ['X-ID' => ['3', '4']]],
            'simple_explode_object'    => [['X-ID' => 'a=b,c=d'], ['X-ID' => '{X-ID*}'], ['X-ID' => ['a' => 'b', 'c' => 'd']]],
        ];
        // phpcs:enable
    }

    /**
     * @param array<string, string> $templates
     */
    #[DataProvider('cookieVariableProvider')]
    public function testGetCookieVariablesPopulatesCookieParams(array $cookies, array $templates, array $expected): void
    {
        $uri     = new Uri('http://example.com/');
        $request = $this->getRequest($uri, [], $cookies);

        $actual = OperationUtil::getCookieVariables($this->uriTemplate, $templates, $request);
        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{0: array, 1: array<string, string>, 2: array}>
     */
    public static function cookieVariableProvider(): array
    {
        return [
            'form_explode_primitive' => [['id' => '5'], ['id' => '{id*}'], ['id' => ['5']]],
            'form_explode_empty'     => [[], ['id' => '{id*}'], ['id' => null]],
            'form_primitive'         => [['id' => '5'], ['id' => '{id}'], ['id' => '5']],
            'form_array'             => [['id' => '3,4'], ['id' => '{id}'], ['id' => ['3', '4']]],
            'form_empty'             => [[], ['id' => '{id}'], ['id' => null]],
            'space_in_name'          => [['a_b' => '5'], ['a b' => '{a b}'], ['a b' => '5']],
            'dot_in_name'            => [['a_b' => '5'], ['a.b' => '{a.b}'], ['a.b' => '5']],
            'url_encoded'            => [['id' => 'a+b'], ['id' => '{id*}'], ['id' => ['a b']]],
        ];
    }

    #[DataProvider('castToScalarProvider')]
    public function testCastToScalar(mixed $value, string $type, mixed $expected): void
    {
        $data   = ['test' => $value];
        $actual = OperationUtil::castToScalar($data, 'test', $type);
        self::assertSame($expected, $actual['test']);
    }

    /**
     * @return array<string, array{0: array|null|string, 1: string, 2: scalar|null}>
     */
    public static function castToScalarProvider(): array
    {
        return [
            'array'      => [['100'], 'int', 100],
            'null'       => [null, 'int', null],
            'bool_one'   => ['1', 'bool', true],
            'bool_zero'  => ['0', 'bool', false],
            'bool_empty' => ['', 'bool', false],
            'float'      => ['12.34', 'float', 12.34],
            'int'        => ['123', 'int', 123],
            'string'     => ['123', 'string', '123'],
        ];
    }

    public function testCastToScalarIgnoresMissingKey(): void
    {
        $expected = ['foo' => 'a'];
        $actual   = OperationUtil::castToScalar($expected, 'bar', 'int');
        self::assertSame($expected, $actual);
    }

    public function testCastArrayToScalarCastsArray(): void
    {
        $expected = [
            'test' => [
                'foo' => 123,
                'bar' => null,
            ],
        ];
        $values   = [
            'test' => [
                'foo' => '123',
                'bar' => null,
            ],
        ];

        $actual = OperationUtil::castToScalarArray($values, 'test', 'int');
        self::assertSame($expected, $actual);
    }

    public function testCastArrayToScalarIgnoresMissingKey(): void
    {
        $expected = ['foo' => 'a'];
        $actual   = OperationUtil::castToScalarArray($expected, 'bar', 'int');
        self::assertSame($expected, $actual);
    }

    public function testListToAssociativeArrayReturnsArray(): void
    {
        $expected = [
            'test' => ['role' => 'admin', 'firstName' => 'Alex'],
        ];

        $actual = OperationUtil::listToAssociativeArray(['test' => ['role', 'admin', 'firstName', 'Alex']], 'test');
        self::assertSame($expected, $actual);
    }

    public function testListToAssociativeArrayHandlesNull(): void
    {
        $expected = [];

        $actual = OperationUtil::listToAssociativeArray(['test' => null], 'test');
        self::assertSame($expected, $actual);
    }

    public function testListToAssociativeArrayHandlesMissingValue(): void
    {
        $expected = [
            'test' => ['role' => 'admin', 'firstName' => null],
        ];

        $actual = OperationUtil::listToAssociativeArray(['test' => ['role', 'admin', 'firstName']], 'test');
        self::assertSame($expected, $actual);
    }

    private function getRequest(Uri $uri, array $headers, array $cookies): ServerRequest
    {
        $stream = $this->createStub(StreamInterface::class);
        return new ServerRequest([], [], $uri, null, $stream, $headers, $cookies);
    }
}
