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

        return [
            new Local,
            function($path) {
                @unlink($path);
                @mkdir(dirname($path), 0777, true);
                file_put_contents($path, 'some content');
            },
            function($path) {
                expect(filesize($path))->toBe(10000 * 3);
            },
        ];
    }
}
