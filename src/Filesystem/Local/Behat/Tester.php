<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\Local\Behat;

class Tester implements \features\Context\Tester
{
    /** @var string */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc
     */
    public function getFileSize(string $path): int
    {
        return filesize($this->basePath . $path);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists(string $path): bool
    {
        return file_exists($this->basePath . $path);
    }
}
