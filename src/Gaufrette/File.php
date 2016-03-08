<?php

declare(strict_types=1);

namespace Gaufrette;

final class File implements \IteratorAggregate
{
    private $path;
    private $content;
    private $size;
    private $metadata;
    private $mode;

    public function __construct(string $path, callable $content, Metadata $metadata = null, Mode $mode = null)
    {
        $this->path = $path;
        $this->content = $content;
        $this->metadata = $metadata;
        $this->mode = $mode;
    }

    public function setContent(callable $content)
    {
        $this->content = $content;
        $this->size = null;
    }

    private function calculateSize($content): int
    {
        $size = 0;
        $chunks = call_user_func($content);
        foreach ($chunks as $chunk) {
            $size += mb_strlen($chunk, '8bit');
        }

        return $size;
    }

    /**
     * @TODO: think of any other way to retrieve the size in a lazy way
     * For exemple, local adapter could use filesize and aws s3 stream headers
     */
    public function getSize(): int
    {
        if (null !== $this->size) {
            return $this->size;
        }
        return $this->size = $this->calculateSize($this->content);
    }

    public function getIterator(): \Iterator
    {
        return call_user_func($this->content);
    }

    public function getPath()
    {
        return $this->path;
    }
}
