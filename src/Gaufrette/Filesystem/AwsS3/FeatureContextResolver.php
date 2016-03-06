<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\AwsS3;

use Behat\Behat\Context\Argument\ArgumentResolver;
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

        return [
            new AwsS3\Filesystem($client, $bucket, $basePath),
            function($path) use($client, $bucket, $basePath) {
                if (!$client->doesBucketExist($bucket)) {
                    $client->createBucket([
                        'Bucket' => $bucket,
                    ]);
                }

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
            function($list) use($client, $bucket, $basePath) {
                $files = iterator_to_array($list);
                expect($files['base/path/complex/tree/1.txt'])->toHaveType(File::class);
                expect($files['base/path/complex/tree/structure/2.txt'])->toHaveType(File::class);
            }
        ];
    }
}
