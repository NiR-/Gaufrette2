<?php

namespace Gaufrette\Filesystem\Local;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Directory;
use Gaufrette\File;
use Gaufrette\Filesystem\Local;

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
                @mkdir(\Gaufrette\dirname($basePath.$path), 0777, true);
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
                file_put_contents($basePath.'complex/tree/structure/2.txt', 'another file');
            },
            function(Directory $directory) use($basePath) {
                $list = iterator_to_array($directory);
                expect(array_keys($list))->toBe([
                    'complex/tree/structure',
                    'complex/tree/1.txt',
                ]);
                expect($list['complex/tree/1.txt'])->toHaveType(File::class);
                expect($list['complex/tree/structure'])->toHaveType(Directory::class);
            },
            function(\Iterator $completeList) use($basePath) {
                $list = iterator_to_array($completeList);
                expect(array_keys($list))->toBe([
                    'complex/tree',
                    'complex/tree/structure/2.txt',
                    'complex/tree/structure',
                    'complex/tree/1.txt',
                ]);
                expect($list['complex/tree'])->toHaveType(Directory::class);
                expect($list['complex/tree/1.txt'])->toHaveType(File::class);
                expect($list['complex/tree/structure'])->toHaveType(Directory::class);
                expect($list['complex/tree/structure/2.txt'])->toHaveType(File::class);
            }
        ];
    }
}
