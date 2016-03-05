<?php

namespace features\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Gaufrette\Filesystem;
use Gaufrette\File;

final class Infrastructure implements Context, SnippetAcceptingContext
{
    private $source;
    private $dest;
    private $fs;

    public function __construct(Filesystem $fs, callable $source, callable $dest)
    {
        $this->source = $source;
        $this->dest = $dest;
        $this->fs = $fs;
    }

    /**
     * @Given a file stored at ":path"
     */
    public function aFileStoredAt($path)
    {
        $this->path = $path;
        ($this->source)($path);
    }

    /**
     * @When I ask for this file
     */
    public function iAskForThisFile()
    {
        $this->file = $this->fs->read($this->path);
    }

    /**
     * @Then I should get the corresponding file object
     */
    public function iShouldGetTheCorrespondingFileObject()
    {
        $file = $this->fs->read($this->path);
//        expect(implode(iterator_to_array($file)))->toBe('some content');
    }

    /**
     * @Given a file object for ":path"
     */
    public function aFileObjectFor($path)
    {
        $this->path = $path;
        $this->file = new File($path, function(){
            for($i = 0; $i < 1000; $i++) {
                yield 'abc';
            }
        });
    }

    /**
     * @When I write it
     */
    public function iWriteIt()
    {
        $this->fs->write($this->file);
    }

    /**
     * @Then it should be stored
     */
    public function itShouldBeStored()
    {
        ($this->dest)($this->path);
    }
}
