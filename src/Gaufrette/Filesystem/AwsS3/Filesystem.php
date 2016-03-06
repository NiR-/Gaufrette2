<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\AwsS3;

use Aws\Exception\MultipartUploadException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Gaufrette\Exception;
use Gaufrette\File;
use Gaufrette\Filesystem\AwsS3\Exception\CouldNotCreateBucket;

final class Filesystem implements \Gaufrette\Filesystem
{
    /** @var S3Client */
    private $s3Client;

    /** @var string */
    private $bucket;

    /** @var string */
    private $basePath;

    /** @var int */
    private $chunkSize;

    /** @var bool */
    private $bucketExists = false;

    /**
     * @param S3Client $client
     * @param string   $bucket
     * @param string   $basePath
     * @param int      $chunkSize
     */
    public function __construct(
        S3Client $client,
        string $bucket,
        string $basePath = '',
        int $chunkSize = MultipartUploader::PART_MIN_SIZE
    ) {
        $this->s3Client  = $client;
        $this->bucket    = $bucket;
        $this->basePath  = $basePath;
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $path): File
    {
        $this->ensureBucketExists();

        return new File($path, $this->iterate($path));
    }

    /**
     * {@inheritdoc}
     */
    public function write(File $file)
    {
        $this->ensureBucketExists();

        try {
            (new MultipartUploader($this->s3Client, $file->getIterator(), [
                'bucket' => $this->bucket,
                'key' => $this->absolutify($file->getPath()),
                'part_size' => $this->chunkSize,
            ]))->upload();
        } catch (MultipartUploadException $e) {
            throw Exception\CouldNotWrite::create($this, $file->getPath(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(File $file)
    {
        $this->ensureBucketExists();

        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $this->absolutify($file->getPath()),
            ]);
        } catch (S3Exception $previous) {
            throw Exception\CouldNotDelete::create($this, $file->getPath(), $previous);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function list(string $path = ''): \Iterator
    {
        $this->ensureBucketExists();

        try {
            $files = $this->s3Client->getIterator('ListObjects', [
                'Bucket' => $this->bucket,
                'Prefix' => $this->absolutify($path)
            ]);

            foreach ($files as $file) {
                yield $file['Key'] => $this->read($file['Key']);
            }
        } catch (S3Exception $previous) {
            throw Exception\CouldNotList::create($this, $path, $previous);
        }
    }

    /**
     * @param string $path
     *
     * @return callable
     */
    private function iterate($path): callable
    {
        return function () use ($path) {
            $command = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $this->absolutify($path),
                '@http'  => ['stream' => true],
            ]);

            try {
                /** @var \GuzzleHttp\Psr7\Stream $stream */
                $stream = $this->s3Client->execute($command)['Body'];
            } catch (S3Exception $exception) {
                throw Exception\CouldNotOpen::create($this, $path, $exception);
            }

            while ($chunk = $stream->read($this->chunkSize)) {
                yield $chunk;
            }

            if (false === $chunk) {
                throw Exception\CouldNotRead::create($this, $path);
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

        try {
            $this->s3Client->createBucket([
                'Bucket' => $this->bucket,
                'LocationConstraint' => $this->s3Client->getRegion(),
            ]);
            $this->bucketExists = true;
        } catch (S3Exception $e) {
            throw CouldNotCreateBucket::create($this->bucket, $e);
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function absolutify(string $path)
    {
        return sprintf('%s/%s', trim($this->basePath, '/'), ltrim($path, '/'));
    }
}
