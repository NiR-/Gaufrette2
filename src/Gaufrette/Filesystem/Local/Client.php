<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\Local;

class Client
{
    public function fopen(string $path, $mode)
    {
        $pointer = @fopen($path, $mode);
        if ($pointer) {
            $this->fseek($pointer, 0);
        }

        return $pointer;
    }

    public function fwrite($pointer, string $chunk)
    {
        return fwrite($pointer, $chunk);
    }

    public function fread($pointer, int $length)
    {
        return fread($pointer, $length);
    }

    public function fclose($pointer)
    {
        return fclose($pointer);
    }

    public function fseek($pointer, int $position)
    {
        return fseek($pointer, $position);
    }

    public function mkdir(string $path)
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, 0777, true);
    }

    public function unlink(string $path)
    {
        return @unlink($path);
    }

    /**
     * @param string $path
     *
     * @return \DirectoryIterator
     *
     * @throws \UnexpectedValueException If $path does not exists
     */
    public function list(string $path): \DirectoryIterator
    {
        return new \DirectoryIterator($path);
        /*return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );*/
    }

    public function find(string $path): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
    }
}
