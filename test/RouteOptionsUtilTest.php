<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi;

use Kynx\Mezzio\OpenApi\RouteOptionsUtil;
use KynxTest\Mezzio\OpenApi\MezzioRequestTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouteOptionsUtil::class)]
final class RouteOptionsUtilTest extends TestCase
{
    use MezzioRequestTrait;

    public function testGetJsonPointerReturnsPointer(): void
    {
        $expected = '/paths/~1pet~1{petId}/get';
        $request  = $this->getOperationRequest($expected);

        $actual = RouteOptionsUtil::getJsonPointer($request);
        self::assertSame($expected, $actual);
    }

    public function testGetJsonPointerReturnsNull(): void
    {
        $request = $this->getNonOperationRequest('/non/operation');
        $actual  = RouteOptionsUtil::getJsonPointer($request);
        self::assertNull($actual);
    }
}
