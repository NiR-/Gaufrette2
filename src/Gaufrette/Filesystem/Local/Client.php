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

    public function fwrite($pointer, $chunk)
    {
        return fwrite($pointer, $chunk);
    }

    public function fread($pointer, $length)
    {
        return fread($pointer, $length);
    }

    public function fclose($pointer)
    {
        return fclose($pointer);
    }

    public function fseek($pointer, $position)
    {
        return fseek($pointer, $position);
    }

    public function mkdir($path)
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, 0777, true);
    }

    public function unlink($path)
    {
        return @unlink($path);
    }

    /**
     * @param string $path
     *
     * @return \RecursiveIteratorIterator
     *
     * @throws \UnexpectedValueException
     */
    public function list(string $path)
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
