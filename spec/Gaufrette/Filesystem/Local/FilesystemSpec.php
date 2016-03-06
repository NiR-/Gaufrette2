<?php

namespace spec\Gaufrette\Filesystem\Local;

use Gaufrette\Exception\CouldNotDelete;
use Gaufrette\Exception\CouldNotList;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Gaufrette\Filesystem\Local\Client;
use Gaufrette\Exception\CouldNotOpen;
use Gaufrette\Exception\CouldNotRead;
use Gaufrette\File;
use Gaufrette\Exception\CouldNotWrite;

class FilesystemSpec extends ObjectBehavior
{
    function let(Client $client)
    {
        $this->beConstructedWith('/base/path', $client);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('Gaufrette\Filesystem');
        $this->shouldHaveType('Gaufrette\Filesystem\Local\Filesystem');
    }

    function it_throws_if_could_not_open($client)
    {
        $client->fopen('/base/path/a/path', 'r')->willReturn(false);
        $file = $this->read('a/path');
        $file->shouldThrow(CouldNotOpen::class)->during('getSize');
    }

    function it_throws_if_could_not_read($client)
    {
        $client->fopen('/base/path/a/path', 'r')->willReturn('a resource');
        $client->fread('a resource', 1024)->willReturn(false);
        $client->fclose('a resource')->willReturn(true);

        $file = $this->read('a/path');
        $file->shouldThrow(CouldNotRead::class)->during('getSize');
    }

    function it_throws_if_could_not_open_for_write($client)
    {
        $client->fopen('/base/path/a/path', 'w+')->willReturn(false);

        $this->shouldThrow(CouldNotOpen::class)->during('write', [new File('a/path', function() {
            return new \ArrayIterator([]);
        })]);
    }

    function it_throws_if_could_not_write($client)
    {
        $client->fopen('/base/path/a/path', 'w+')->willReturn('a resource');
        $client->mkdir('/base/path/a')->willReturn(true);
        $client->fwrite('a resource', 'a')->willReturn(false);
        $client->fclose('a resource')->willReturn(true);

        $this->shouldThrow(CouldNotWrite::class)->during('write', [new File('a/path', function() {
            return new \ArrayIterator(['a']);
        })]);
    }

    function it_throws_if_could_not_delete($client)
    {
        $client->unlink('/base/path/a/path')->willReturn(false);
        $this->shouldThrow(CouldNotDelete::class)->during('delete', [new File('a/path', function () {})]);
    }

    function it_throws_if_could_not_list($client)
    {
        $client->list('/base/path/a/complex/path')->willThrow('Exception');
        $generator = $this->list('does/not/exists/');
        $generator->shouldThrow(CouldNotList::class)->during('current');
    }
}
