<?php

declare (strict_types = 1);

namespace Gaufrette\Filesystem\AwsS3\Behat;

use Aws\S3\S3Client;
use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Filesystem\AwsS3\Filesystem;

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
                'key' => getenv('AWS_ACCESS_KEY'),
                'secret' => getenv('AWS_SECRET_KEY'),
            ],
        ]);

        $bucket = getenv('AWS_BUCKET_NAME');
        $basePath = 'base/path/';

        return [
            new Filesystem($client, $bucket, $basePath),
            new Initializer($client, $bucket, $basePath),
            new Tester($client, $bucket, $basePath)
        ];
    }
}
