<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\AwsS3\Behat;

use Aws\S3\S3Client;

class Tester implements \features\Context\Tester
{
    /** @var S3Client */
    private $client;

    /** @var string */
    private $bucket;

    /** @var string */
    private $basePath;

    /**
     * @param S3Client $client
     * @param string   $bucket
     * @param string   $basePath
     */
    public function __construct(S3Client $client, string $bucket, string $basePath)
    {
        $this->client   = $client;
        $this->bucket   = $bucket;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileSize(string $path): int
    {
        return (int) $this->client->headObject([
            'Bucket' => $this->bucket,
            'Key' => $this->basePath . ltrim($path, '/'),
        ])['ContentLength'];
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists(string $path): bool
    {
        return $this->client->doesObjectExist($this->bucket, $this->basePath . ltrim($path, '/'));
    }
}

