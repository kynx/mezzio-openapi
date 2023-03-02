<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Schema;

use cebe\openapi\spec\OpenApi;

use Webimpress\SafeWriter\Exception\ExceptionInterface;
use Webimpress\SafeWriter\FileWriter;

use function file_exists;
use function serialize;
use function str_replace;
use function unlink;

final class FileCache implements CacheInterface
{
    private const CACHE_TEMPLATE = <<<'EOT'
        <?php
        
        /**
         * This OpenApi cache file was generated by %s
         * at %s
         */
        %s
        
        EOT;

    public function __construct(private readonly string $path)
    {
    }

    public function get(): ?OpenApi
    {
        if (file_exists($this->path)) {
            /** @var OpenApi|null $schema */
            $schema = require $this->path;
            if ($schema instanceof OpenApi) {
                return $schema;
            }
        }

        return null;
    }

    public function set(OpenApi $openApi): void
    {
        $contents = sprintf(
            self::CACHE_TEMPLATE,
            self::class,
            date('c'),
            "return unserialize('" . str_replace("'", "\\'", serialize($openApi)) . "');\n"
        );

        try {
            FileWriter::writeFile($this->path, $contents);
        } catch (ExceptionInterface $exception) {
            // ignore errors writing cache;
        }
    }

    public function clear(): void
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }
}