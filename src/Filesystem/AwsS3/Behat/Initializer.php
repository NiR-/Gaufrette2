<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\AwsS3\Behat;

use Aws\S3\S3Client;

class Initializer extends \features\Context\Initializer
{
    /** @var S3Client */
    private $client;

    /** @var string */
    private $bucket;

    /** @var string */
    private $basePath;

    /**
     * @param S3Client $client
     * @param string $bucket
     * @param string $basePath
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
    public function initFileToBeRead(string $path)
    {
        $this->ensureBucketExists();

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->basePath.ltrim($path, '/'),
            'Body' => 'some content',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function initTreeStructure()
    {
        $this->ensureBucketExists();

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->basePath.'complex/tree/1.txt',
            'Body' => 'some content',
        ]);

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->basePath.'complex/tree/structure/2.txt',
            'Body' => 'another file',
        ]);
    }

    private function ensureBucketExists()
    {
        if ($this->client->doesBucketExist($this->bucket)) {
            return;
        }

        $this->client->createBucket([
            'Bucket' => $this->bucket,
        ]);
    }
}
