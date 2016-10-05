<?php

namespace Gaufrette;

final class Directory implements \IteratorAggregate
{
    /** @var string */
    private $path;

    /** @var callable */
    private $content;

    /**
     * @param string   $path
     * @param callable $content
     */
    public function __construct(string $path, callable $content)
    {
        $this->path    = '/' . trim($path, '/') . '/';
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return call_user_func($this->content);
    }
}
