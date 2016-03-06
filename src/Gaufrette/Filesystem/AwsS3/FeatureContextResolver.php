<?php

namespace Gaufrette\Filesystem\AwsS3;

use Behat\Behat\Context\Argument\ArgumentResolver;
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
            'region' => 'eu-west-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY'),
                'secret' => getenv('AWS_SECRET_KEY'),
            ],
        ]);

        $bucket = uniqid();
        $basePath = 'base/path/';

        return [
            new AwsS3\Filesystem($client, $bucket, $basePath),
            function($path) use($client, $bucket, $basePath) {
                $client->createBucket([
                    'Bucket' => $bucket,
                ]);
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $basePath.ltrim($path, '/'),
                    'Body' => 'some content',
                ]);
            },
            function($path) use($client, $bucket, $basePath) {
                expect(strlen($client->getObject([
                    'Bucket' => $bucket,
                    'Key' => $basePath.ltrim($path, '/'),
                ])['Body']))->toBe(1024 * 3);
            },
            function($path) use($client, $bucket, $basePath) {
                expect($client->doesObjectExist($bucket, $basePath.ltrim($path, '/')))->toBe(false);
            }
        ];
    }
}
