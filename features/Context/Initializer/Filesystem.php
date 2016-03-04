<?php

namespace features\Context\Initializer;

use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Filesystem\Local;
use features\Context\Infrastructure;

final class Filesystem implements ArgumentResolver
{
    public function resolveArguments(\ReflectionClass $reflection, array $arguments)
    {
        if ($reflection->getName() !== Infrastructure::class) {
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
                expect(filesize($basePath.$path))->toBe(10000 * 3);
            },
        ];
    }
}
