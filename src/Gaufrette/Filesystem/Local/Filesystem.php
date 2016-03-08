<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\Local;

use Gaufrette\Directory;
use Gaufrette\Exception\CouldNotDelete;
use Gaufrette\Exception\CouldNotList;
use Gaufrette\Exception\CouldNotOpen;;
use Gaufrette\Exception\CouldNotRead;
use Gaufrette\Exception\CouldNotWrite;
use Gaufrette\Exception\DirectoryDoesNotExists;
use Gaufrette\File;

final class Filesystem implements \Gaufrette\Filesystem
{
    /** @var Client */
    private $client;

    /** @var string */
    private $basePath;

    /** @var int */
    private $chunkSize;

    /**
     * @param string      $basePath
     * @param Client|null $client
     * @param int         $chunkSize
     */
    public function __construct(string $basePath, Client $client = null, $chunkSize = 1024)
    {
        $this->basePath = $basePath;
        $this->client = $client ?: new Client;
        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $path): File
    {
        return new File(ltrim($path, '/'), $this->iterate($path));
    }

    /**
     * {@inheritdoc}
     */
    public function readDirectory(string $path): Directory
    {
        return new Directory(ltrim($path, '/'), $this->iterateDirectory($path));
    }

    /**
     * {@inheritdoc}
     */
    public function write(File $file)
    {
        if (!$pointer = $this->client->fopen($this->absolutify($file->getPath()), 'w+')) {
            throw CouldNotOpen::create($this, $file->getPath());
        }

        $dirname = \Gaufrette\dirname($file->getPath());

        try {
            if (!$this->client->mkdir($this->absolutify($dirname))) {
                throw CouldNotWrite::create($this, $dirname);
            }
            foreach ($file as $chunk) {
                if (false === $this->client->fwrite($pointer, $chunk)) {
                    throw CouldNotWrite::create($this, $file->getPath());
                }
            }
        }
        finally {
            $this->client->fclose($pointer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(File $file)
    {
        if (!$this->client->unlink($this->absolutify($file->getPath()))) {
            throw CouldNotDelete::create($this, $file->getPath());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $path, string $pattern = ''): \Iterator
    {
        if (!empty($pattern)) {
            throw new \InvalidArgumentException('Pattern is not supported by the Local adapter for now.');
        }

        try {
            $iterator = $this->client->find($this->absolutify($path));
        } catch (\UnexpectedValueException $e) {
            throw DirectoryDoesNotExists::create($path, $e);
        }

        return $this->hydrateDirectoryList($iterator, $this->readDirectory($path));
    }

    /**
     * @param string $path
     *
     * @return callable
     */
    private function iterate(string $path): callable
    {
        return function() use($path) {
            if (!$pointer = $this->client->fopen($this->absolutify($path), 'r')) {
                throw CouldNotOpen::create($this, $path);
            }

            try {
                while ($chunk = $this->client->fread($pointer, $this->chunkSize)) {
                    yield $chunk;
                }
                if (false === $chunk) {
                    throw CouldNotRead::create($this, $path);
                }
            }
            finally {
                $this->client->fclose($pointer);
            }
        };
    }

    private function iterateDirectory(string $path): callable
    {
        return function () use ($path) {
            try {
                $iterator = $this->client->list($this->absolutify($path));
            } catch (\UnexpectedValueException $e) {
                throw DirectoryDoesNotExists::create($path, $e);
            }

            return $this->hydrateDirectoryList($iterator);
        };
    }

    private function hydrateDirectoryList(\Iterator $iterator, Directory $directory = null): \Iterator
    {
        $basePathLen = strlen($this->basePath);

        if ($directory) {
            yield $directory->getPath() => $directory;
        }

        foreach ($iterator as $item) {
            $relativePath = substr($iterator->getPathname(), $basePathLen);

            if ($item->isFile()) {
                yield $relativePath => $this->read($relativePath);
            } elseif (!$iterator->isDot()) {
                yield $relativePath => $this->readDirectory($relativePath);
            }
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function absolutify(string $path)
    {
        return sprintf('%s/%s', rtrim($this->basePath, '/'), ltrim($path, '/'));
    }
}
