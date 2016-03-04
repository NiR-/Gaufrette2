<?php
namespace spec\Gaufrette;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Gaufrette\Metadata;
use Gaufrette\Mode;

class FileSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('/path/to/file', function() { return []; }, new Metadata, new Mode);
        $this->shouldHaveType('Gaufrette\File');
    }

    function it_calculates_size()
    {
        $this->beConstructedWith('/path/to/file', function() {
            foreach (range(0, 1000) as $i) {
                yield 'abc';
            }
        }, new Metadata, new Mode);
        $this->getSize()->shouldBe(3003);
    }
}
