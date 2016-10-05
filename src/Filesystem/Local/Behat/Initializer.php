<?php

declare(strict_types=1);

namespace Gaufrette\Filesystem\Local\Behat;

class Initializer extends \features\Context\Initializer
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
     * {@inheritdoc}
     */
    public function initFileToBeRead(string $path)
    {
        $fullPath = $this->basePath . $path;

        @unlink($fullPath);
        @mkdir(\Gaufrette\dirname($fullPath), 0777, true);
        file_put_contents($fullPath, 'some content');
    }

    /**
     * {@inheritdoc}
     */
    public function initTreeStructure()
    {
        @mkdir($this->basePath.'complex/tree/structure', 0777, true);
        file_put_contents($this->basePath.'complex/tree/1.txt', 'some content');
        file_put_contents($this->basePath.'complex/tree/structure/2.txt', 'another file');
    }
}
