<?php

namespace Gaufrette\Filesystem\Local\Behat;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Filesystem\Local\Filesystem;

final class FeatureContextResolver implements ArgumentResolver
{
    public function resolveArguments(\ReflectionClass $reflection, array $arguments)
    {
        if (!isset($arguments['fs'])) {
            return $arguments;
        }
        if ($arguments['fs'] !== 'local') {
            return $arguments;
        }

        $basePath = sys_get_temp_dir().'/knp-gaufrette/';

        return [
            new Filesystem($basePath),
            new Initializer($basePath),
            new Tester($basePath)
        ];
    }
}
