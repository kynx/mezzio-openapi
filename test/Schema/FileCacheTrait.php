<?php

declare(strict_types=1);

namespace KynxTest\Mezzio\OpenApi\Schema;

use cebe\openapi\spec\OpenApi;

use function getmypid;
use function microtime;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

trait FileCacheTrait
{
    private string $file;

    protected function getCacheFileName(): string
    {
        if (empty($this->file)) {
            $this->file = tempnam(sys_get_temp_dir(), 'phpunit_' . getmypid() . '_' . microtime(true));
        }
        return $this->file;
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    protected function getOpenApi(): OpenApi
    {
        return new OpenApi([
            'openapi' => '3.0.2',
            'info'    => [
                'title'   => 'Cached API',
                'version' => '1.1.1',
            ],
            'paths'   => [],
        ]);
    }
}
