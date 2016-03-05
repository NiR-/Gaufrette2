<?php

namespace Gaufrette\Filesystem\AwsS3;

use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Gaufrette\Exception\CouldNotOpen;
use Gaufrette\Exception\CouldNotRead;
use Gaufrette\File;
use Gaufrette\StreamClient;

final class Filesystem implements \Gaufrette\Filesystem
{
    /** @var S3Client */
    private $s3Client;

    /** @var StreamClient */
    private $streamClient;

    /** @var string */
    private $bucket;

    /** @var int */
    private $chunkSize;

    /** @var bool */
    private $bucketExists = false;

    public function __construct(S3Client $client, $bucket, StreamClient $streamClient = null, int $chunkSize = MultipartUploader::PART_MIN_SIZE)
    {
        $this->s3Client     = $client;
        $this->bucket       = $bucket;
        $this->streamClient = $streamClient ?: new StreamClient();
        $this->chunkSize    = $chunkSize;

        $client->registerStreamWrapper();
    }

    public function read(string $path): File
    {
        return new File($path, $this->iterate($path));
    }

    public function write(File $file)
    {
        $this->ensureBucketExists();

        $uploader = new MultipartUploader($this->s3Client, $file->getIterator(), [
            'bucket' => $this->bucket,
            'key'    => $file->getPath(),
        ]);
        $uploader->upload();
    }

    private function iterate($path): callable
    {
        return function() use ($path) {
            $this->ensureBucketExists();

            if (!$pointer = $this->streamClient->fopen($path, 'r')) {
                throw CouldNotOpen::create($this, $path);
            }

            try {
                while ($chunk = $this->streamClient->fread($pointer, $this->chunkSize)) {
                    yield $chunk;
                }

                if (false === $chunk) {
                    throw CouldNotRead::create($this, $path);
                }
            }
            finally {
                $this->streamClient->fclose($pointer);
            }
        };
    }

    private function ensureBucketExists()
    {
        if ($this->bucketExists) {
            return;
        }

        if ($this->bucketExists = $this->s3Client->doesBucketExist($this->bucket)) {
            return;
        }

        $this->s3Client->createBucket([
            'Bucket' => $this->bucket,
            'LocationConstraint' => $this->s3Client->getRegion(),
        ]);
        $this->bucketExists = true;
    }
}