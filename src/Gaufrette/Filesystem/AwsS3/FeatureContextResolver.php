<?php

namespace Gaufrette\Filesystem\AwsS3;

use Behat\Behat\Context\Argument\ArgumentResolver;
use Gaufrette\Filesystem\AwsS3;
use Aws\S3\S3Client;

final class FeatureContextResolver implements ArgumentResolver
{
    public function resolveArguments(\ReflectionClass $reflection, array $arguments)
    {
        if (!isset($arguments['fs'])) {
            return $arguments;
        }
        if ($arguments['fs'] !== 's3') {
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

        return [
            new AwsS3\Filesystem($client, $bucket),
            function($path) {
            },
            function($path) use($client, $bucket) {
                expect(strlen($client->getObject([
                    'Bucket' => $bucket,
                    'Key' => $path,
                ])['Body']))->toBe(1000 * 3);
            },
        ];
    }
}
