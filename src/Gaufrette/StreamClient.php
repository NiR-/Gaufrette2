<?php

declare(strict_types=1);

namespace Gaufrette;

class StreamClient
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
}
