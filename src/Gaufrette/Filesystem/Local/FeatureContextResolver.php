<?php

namespace Gaufrette\Filesystem\Local;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\File;
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

        $basePath = sys_get_temp_dir().'/knp-gaufrette/';
        return [
            new Local\Filesystem($basePath),
            function($path) use($basePath){
                @unlink($basePath.$path);
                @mkdir(dirname($basePath.$path), 0777, true);
                file_put_contents($basePath.$path, 'some content');
            },
            function($path) use($basePath){
                expect(filesize($basePath.$path))->toBe(1024 * 3);
            },
            function($path) use($basePath) {
                expect(file_exists($basePath.$path));
            },
            function() use($basePath) {
                @mkdir($basePath.'complex/tree/structure', 0777, true);
                file_put_contents($basePath.'complex/tree/1.txt', 'some content');
                file_put_contents($basePath.'complex/tree/kikou.txt', 'some content');
                file_put_contents($basePath.'complex/tree/structure/2.txt', 'another file');
            },
            function($list) use($basePath) {
                $files = iterator_to_array($list);
                expect($files['/complex/tree/1.txt'])->toHaveType(File::class);
                expect($files['/complex/tree/structure/2.txt'])->toHaveType(File::class);
            }
        ];
    }
}
