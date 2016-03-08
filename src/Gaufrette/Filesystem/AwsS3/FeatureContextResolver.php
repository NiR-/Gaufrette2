<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\AwsS3;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Directory;
use Gaufrette\File;
use Gaufrette\Filesystem\AwsS3;
use Aws\S3\S3Client;

final class FeatureContextResolver implements ArgumentResolver
{
    public function resolveArguments(\ReflectionClass $reflection, array $arguments)
    {
        if (!isset($arguments['fs']) || $arguments['fs'] !== 's3') {
            return $arguments;
        }

        $client = new S3Client([
            'region' => getenv('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY'),
                'secret' => getenv('AWS_SECRET_KEY'),
            ],
        ]);

        $bucket = getenv('AWS_BUCKET_NAME');
        $basePath = 'base/path/';

        if ($client->doesBucketExist($bucket)) {
            $client->deleteMatchingObjects($bucket, $basePath);
            $client->deleteBucket(['Bucket' => $bucket]);
        }
        $client->createBucket(['Bucket' => $bucket]);

        return [
            new AwsS3\Filesystem($client, $bucket, $basePath),
            function($path) use($client, $bucket, $basePath) {
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $basePath.ltrim($path, '/'),
                    'Body' => 'some content',
                ]);
            },
            function($path) use($client, $bucket, $basePath) {
                expect(strlen((string) $client->getObject([
                    'Bucket' => $bucket,
                    'Key' => $basePath.ltrim($path, '/'),
                ])['Body']))->toBe(1024 * 3);
            },
            function($path) use($client, $bucket, $basePath) {
                expect($client->doesObjectExist($bucket, $basePath.ltrim($path, '/')))->toBe(false);
            },
            function() use($client, $bucket, $basePath) {
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $basePath.'complex/tree/1.txt',
                    'Body'   => 'some content',
                ]);
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $basePath.'complex/tree/structure/2.txt',
                    'Body'   => 'another file',
                ]);
            },
            function(Directory $directory) use($client, $bucket, $basePath) {
                $files = iterator_to_array($directory);
                expect(array_keys($files))->toBeLike([
                    'complex/tree',
                    'complex/tree/1.txt',
                    'complex/tree/structure',
                    'complex/tree/structure/2.txt'
                ]);
                expect($files['complex/tree'])->toHaveType(Directory::class);
                expect($files['complex/tree/1.txt'])->toHaveType(File::class);
                expect($files['complex/tree/structure'])->toHaveType(Directory::class);
                expect($files['complex/tree/structure/2.txt'])->toHaveType(File::class);
            },
            function(\Iterator $completeList) use($client, $bucket, $basePath) {
                $list = iterator_to_array($completeList);
                expect(array_keys($list))->toBeLike([
                    'complex/tree',
                    'complex/tree/1.txt',
                    'complex/tree/structure',
                    'complex/tree/structure/2.txt'
                ]);
                expect($list['complex/tree'])->toHaveType(Directory::class);
                expect($list['complex/tree/1.txt'])->toHaveType(File::class);
                expect($list['complex/tree/structure'])->toHaveType(Directory::class);
                expect($list['complex/tree/structure/2.txt'])->toHaveType(File::class);
            }
        ];
    }
}
