<?php

declare(strict_types=1);

namespace Kynx\Mezzio\OpenApi\Schema;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\json\InvalidJsonPointerSyntaxException;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use InvalidArgumentException;
use Kynx\Mezzio\OpenApi\ConfigProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Exception\ParseException;

use function in_array;
use function pathinfo;

use const PATHINFO_EXTENSION;

final class OpenApiFactory
{
    public function __invoke(ContainerInterface $container): OpenApi
    {
        $config   = (array) ($container->get('config')[ConfigProvider::CONFIG_KEY] ?? []);
        $document = (string) ($config[ConfigProvider::SCHEMA_KEY] ?? '');
        $validate = (bool) ($config[ConfigProvider::VALIDATE_KEY]['schema'] ?? true);
        $useCache = (bool) ($config[ConfigProvider::CACHE_KEY]['enabled'] ?? false);

        if ($useCache) {
            $cache = $container->get(CacheInterface::class);
            return $this->getCachedOpenApi($cache, $document, $validate);
        }

        return $this->getOpenApi($document, $validate);
    }

    private function getOpenApi(string $document, bool $validate): OpenApi
    {
        $extension = pathinfo($document, PATHINFO_EXTENSION);
        try {
            if ($extension === 'json') {
                $openApi = Reader::readFromJsonFile($document);
            } elseif (in_array($extension, ['yaml', 'yml'], true)) {
                $openApi = Reader::readFromYamlFile($document);
            } else {
                throw new InvalidArgumentException("Unrecognised schema extension '$extension'");
            }
        } catch (
            InvalidJsonPointerSyntaxException
            | IOException
            | ParseException
            | TypeErrorException
            | UnresolvableReferenceException $exception
        ) {
            throw new InvalidArgumentException("Cannot read specification document '$document", 0, $exception);
        }

        if ($validate && ! $openApi->validate()) {
            throw InvalidOpenApiException::fromOpenApiErrors($openApi->getErrors());
        }

        return $openApi;
    }

    private function getCachedOpenApi(CacheInterface $cache, string $document, bool $validate): OpenApi
    {
        $openApi = $cache->get();
        if ($openApi instanceof OpenApi) {
            return $openApi;
        }

        $openApi = $this->getOpenApi($document, $validate);
        $cache->set($openApi);
        return $openApi;
    }
}
