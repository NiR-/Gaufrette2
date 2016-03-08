<?php

declare(strict_types=1);

namespace Gaufrette\Exception;

use Gaufrette\Filesystem;

final class CouldNotRead extends \Exception implements \Gaufrette\Exception
{
    public static function create(Filesystem $fs, string $path): CouldNotRead
    {
        return new self(sprintf('Filesystem "%s" could not read "%s"', get_class($fs), $path));
    }
}
