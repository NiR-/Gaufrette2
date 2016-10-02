<?php

declare(strict_types=1);

namespace features\Context;

abstract class Initializer
{
    /**
     * Called right before reading a file on the filesystem
     *
     * @param string $path
     *
     * @return void
     */
    public function initFileToBeRead(string $path)
    {
    }

    /**
     * Called right before listing directory content
     *
     * @return void
     */
    public function initTreeStructure()
    {
    }
}
