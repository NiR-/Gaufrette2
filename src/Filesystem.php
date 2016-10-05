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
     * @param string $path
     *
     * @return Directory
     *
     * @throws Exception\DirectoryDoesNotExists
     * @throws Exception\CouldNotList
     *
     * @TODO: What's the difference between both exceptions ?
     */
    public function list(string $path): Directory;

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
     * @param string $path
     *
     * @return \Iterator
     *
     * @throws Exception\CouldNotList
     */
    public function find(string $path): \Iterator;
}
