<?php

declare (strict_types = 1);

namespace Gaufrette;

interface Filesystem
{
    /**
     * @param string $path
     *
     * @return File
     *
     * @throws Exception\CouldNotOpen
     * @throws Exception\CouldNotRead
     */
    public function read(string $path): File;

    /**
     * @param File $file
     *
     * @throws Exception\CouldNotWrite
     */
    public function write(File $file);

    /**
     * @param File $file
     *
     * @throws Exception\CouldNotDelete
     */
    public function delete(File $file);

    /**
     * @param string $directory
     *
     * @return \Iterator
     *
     * @throws Exception\CouldNotList
     */
    public function list(string $directory = ''): \Iterator;
}
