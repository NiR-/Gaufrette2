<?php

namespace Gaufrette\Filesystem;

use Gaufrette\File;
use Gaufrette\Filesystem;

final class Local implements Filesystem
{
    public function read(string $path): File
    {
        return new File($path, $this->iterate($path));
    }

    public function write(File $file)
    {
        if (!$pointer = fopen($file->getPath(), 'w+')) {
            throw new CouldNotOpen($this, $file->getPath());
        }

        try {
            foreach ($file as $chunk) {
                if (false === fwrite($pointer, $chunk)) {
                    throw new CouldNotWrite($this, $file);
                }
            }
        }
        finally {
            fclose($pointer);
        }
    }

    private function iterate($path): callable
    {
        return function() use($path) {
            if (!$pointer = fopen($path, 'r')) {
                throw new CouldNotOpen($this, $path);
            }

            try {
                while ($chunk = fread($pointer, 1024)) {
                    yield $chunk;
                }
            }
            finally {
                fclose($pointer);
            }
        };
    }
}
