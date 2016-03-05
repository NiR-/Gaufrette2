<?php

namespace features\Context\Initializer;

use Aws\S3\S3Client;
use Gaufrette\Filesystem\AwsS3;
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
        /*return [
            new Local\Filesystem($basePath),
            function($path) use ($basePath){
                @unlink($basePath.$path);
                @mkdir(dirname($basePath.$path), 0777, true);
                file_put_contents($basePath.$path, 'some content');
            },
            function($path) use($basePath){
                expect(filesize($basePath.$path))->toBe(10000 * 3);
            },
        ];*/

        $s3Args = [
            'region' => 'eu-west-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => 'AKIAIXZ3H6QDRWG543RQ',
                'secret' => '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',
            ],
        ];

        return [
            new AwsS3\Filesystem(new S3Client($s3Args), uniqid()),
            function () {
                var_dump(func_get_args());
            },
            function () {
                var_dump(func_get_args());
            },
        ];
    }
}
