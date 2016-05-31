<?php

declare (strict_types = 1);

namespace Gaufrette\Filesystem\AwsS3\Exception;

use Gaufrette\Exception;

final class CouldNotCreateBucket extends \Exception implements Exception
{
    /**
     * @param string          $bucket
     * @param \Throwable|null $previous
     *
     * @return CouldNotCreateBucket
     */
    public static function create(string $bucket, \Throwable $previous = null)
    {
        return new self(sprintf('Could not create S3 bucket "%s".', $bucket), 0, $previous);
    }
}
