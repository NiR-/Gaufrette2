<?php

namespace Gaufrette\Exception;

use Gaufrette\Exception;
use Gaufrette\Filesystem;

final class CouldNotList extends \Exception implements Exception
{
    public static function create(Filesystem $filesystem, $path, \Throwable $previous = null)
    {
        return new self(sprintf('Filesystem "%s" could not list "%s".', get_class($filesystem), $path), 0, $previous);
    }
}
