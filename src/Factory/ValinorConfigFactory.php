<?php

declare(strict_types=1);

namespace Sirix\Config\Factory;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Psr\Container\ContainerInterface;

class ValinorConfigFactory
{
    private const DEFAULT_CACHE_DIR = 'data/cache/valinor';

    public static function __callStatic(string $name, array $arguments): mixed
    {
        /** @var ContainerInterface $container */
        /** @var string $serviceName */
        [$container, $serviceName] = $arguments;
        $mapper = self::getMapper($container);
        $options = $container->get($name);

        return $mapper->map($serviceName, Source::array($options)->camelCaseKeys());
    }

    private static function getMapper(ContainerInterface $container): TreeMapper
    {
        static $mapper;
        if (null !== $mapper) {
            return $mapper;
        }

        $config = $container->has('config') ? $container->get('config') : [];

        $isCacheEnabled = false;
        $isDevModeEnabled = false;
        $cacheDirectory = self::DEFAULT_CACHE_DIR;

        if (isset($config['sirix_config']['valinor'])) {
            $isCacheEnabled = $config['sirix_config']['valinor']['cache'] ?? false;
            $cacheDirectory = $config['sirix_config']['valinor']['cache_directory'] ?? self::DEFAULT_CACHE_DIR;
            $isDevModeEnabled = $config['sirix_config']['valinor']['development_mode'] ?? false;
        }

        $mapper = (new MapperBuilder())->allowSuperfluousKeys();
        if ($isCacheEnabled) {
            $cache = new FileSystemCache($cacheDirectory);

            if ($isDevModeEnabled) {
                $cache = new FileWatchingCache($cache);
            }

            return $mapper = $mapper->withCache($cache)->mapper();
        }

        return $mapper = $mapper->mapper();
    }
}
