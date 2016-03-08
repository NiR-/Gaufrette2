<?php

namespace Gaufrette\Exception;

use Gaufrette\Exception;

class DirectoryDoesNotExists extends \Exception implements Exception
{
    public static function create(string $path, \Throwable $previous = null): DirectoryDoesNotExists
    {
        return new self(sprintf('Directory "%s" does not exists.', $path), 0, $previous);
    }
}
