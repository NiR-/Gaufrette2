<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\Local;

use Gaufrette\File;
use Gaufrette\Exception\CouldNotOpen;
use Gaufrette\Filesystem\Local\Client;
use Gaufrette\Exception\CouldNotRead;
use Gaufrette\Exception\CouldNotWrite;

final class Filesystem implements \Gaufrette\Filesystem
{
    private $client;
    private $basePath;
    private $chunkSize;

    public function __construct(string $basePath, Client $client = null, $chunkSize = 1024)
    {
        $this->basePath = $basePath;
        $this->client = $client ?: new Client;
        $this->chunkSize = $chunkSize;
    }

    public function read(string $path): File
    {
        return new File($path, $this->iterate($path));
    }

    public function write(File $file)
    {
        if (!$pointer = $this->client->fopen($this->absolutify($file->getPath()), 'w+')) {
            throw CouldNotOpen::create($this, $file->getPath());
        }

        try {
            if (!$this->client->mkdir($this->absolutify(dirname($file->getPath())))) {
                throw CouldNotWrite::create($this, dirname($file->getPath()));
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

    private function iterate($path): callable
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

    private function absolutify(string $path)
    {
        return sprintf('%s/%s', rtrim($this->basePath, '/'), ltrim($path, '/'));
    }
}
