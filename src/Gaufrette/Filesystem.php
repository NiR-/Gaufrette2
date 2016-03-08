<?php

declare(strict_types=1);

namespace Gaufrette;

/**
 * @TODO: remove useless throws phpdoc on lazy methods
 */
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
     */
    public function readDirectory(string $path): Directory;

    /**
     * Find a specific pattern in a directory and its children
     *
     * @param string $path
     * @param string $pattern
     *
     * @return \Iterator
     *
     * @throws Exception\DirectoryDoesNotExists
     * @throws Exception\CouldNotList
     */
    public function find(string $path, string $pattern = ''): \Iterator;

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
}
