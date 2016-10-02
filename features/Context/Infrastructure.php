<?php

namespace features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Gaufrette\Filesystem;
use Gaufrette\File;

final class Infrastructure implements Context, SnippetAcceptingContext
{
    /** @var Filesystem */
    private $fs;

    /** @var Initializer */
    private $initializer;

    /** @var Tester */
    private $tester;

    private $path;
    private $file;
    private $list;

    public function __construct(Filesystem $fs, Initializer $initializer, Tester $tester)
    {
        $this->fs          = $fs;
        $this->initializer = $initializer;
        $this->tester      = $tester;
    }

    /**
     * @Given a file stored at ":path"
     */
    public function aFileStoredAt($path)
    {
        $this->path = $path;
        $this->initializer->initFileToBeRead($path);
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
        expect(implode(iterator_to_array($file)))->toBe('some content');
    }

    /**
     * @Given a file object for ":path"
     */
    public function aFileObjectFor($path)
    {
        $this->path = $path;

        // Create a file with 3 chunks of 1024 "a"
        $this->file = new File($path, function(){
            for($i = 0; $i < 3; $i++) {
                yield implode(array_fill(0, 1024, 'a'));
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
        expect($this->tester->getFileSize($this->path))->toBe(3 * 1024);
    }

    /**
     * @When I delete it
     */
    public function iDeleteIt()
    {
        $this->fs->delete($this->file);
    }

    /**
     * @Then it should be deleted
     */
    public function itShouldBeDeleted()
    {
        expect($this->tester->fileExists($this->path))->toBe(false);
    }

    /**
     * @Given there is a complex tree structure
     */
    public function thereIsAComplexTreeStructure()
    {
        $this->initializer->initTreeStructure();
    }

    /**
     * @When I list
     */
    public function iList()
    {
        $this->list = $this->fs->list();
    }

    /**
     * @Then I should see the complex tree structure
     */
    public function iShouldSeeTheComplexTreeStructure()
    {
        $files = iterator_to_array($this->list);
        $keys = array_keys($files);
        sort($keys);

        expect($keys)->toBeLike([
            '/complex/tree/1.txt',
            '/complex/tree/structure/2.txt',
        ]);
        expect($files['/complex/tree/1.txt'])->toHaveType(File::class);
        expect($files['/complex/tree/structure/2.txt'])->toHaveType(File::class);
    }
}
