<?php

namespace Gaufrette\Filesystem\Local;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Filesystem\AwsS3;
use Gaufrette\Filesystem\Local;
use Aws\S3\S3Client;

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

        $basePath = sys_get_temp_dir().'/';
        return [
            new Local\Filesystem($basePath),
            function($path) use ($basePath){
                @unlink($basePath.$path);
                @mkdir(dirname($basePath.$path), 0777, true);
                file_put_contents($basePath.$path, 'some content');
            },
            function($path) use($basePath){
                expect(filesize($basePath.$path))->toBe(1000 * 3);
            },
        ];
    }
}
