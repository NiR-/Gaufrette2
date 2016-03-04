<?php

declare(strict_types=1);

namespace Gaufrette;

use Gaufrette\File;

interface Filesystem
{
    public function read(string $path): File;

    public function write(File $file);
}
