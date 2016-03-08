<?php

declare(strict_types=1);

namespace Gaufrette\Exception;

use Gaufrette\Filesystem;

final class CouldNotOpen extends \Exception implements \Gaufrette\Exception
{
    public static function create(Filesystem $fs, string $path, \Throwable $previous = null): CouldNotOpen
    {
        return new self(sprintf('Filesystem "%s" could not open "%s"', get_class($fs), $path), 0, $previous);
    }
}
