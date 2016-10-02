<?php

namespace spec\Gaufrette\Filesystem\AwsS3;

use Aws\CommandInterface;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Gaufrette\Exception\CouldNotDelete;
use Gaufrette\Exception\CouldNotList;
use Gaufrette\Exception\CouldNotOpen;
use Gaufrette\Exception\CouldNotRead;
use Gaufrette\File;
use Gaufrette\Filesystem\AwsS3\Exception\CouldNotCreateBucket;
use GuzzleHttp\Psr7\Stream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilesystemSpec extends ObjectBehavior
{
    function let(S3Client $client)
    {
        $this->beConstructedWith($client, 'bucket-name', '/base/path', 1024);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('Gaufrette\Filesystem');
        $this->shouldHaveType('Gaufrette\Filesystem\AwsS3\Filesystem');
    }

    function it_creates_the_bucket_if_it_does_not_exists($client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(false);
        $client->getRegion()->willReturn('eu-west-1');
        $client->createBucket(['Bucket' => 'bucket-name', 'LocationConstraint' => 'eu-west-1'])->shouldBeCalled();

        $this->read('a/path');
    }

    function it_throws_if_could_not_create_bucket($client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(false);
        $client->getRegion()->willReturn('eu-west-1');
        $client
            ->createBucket(['Bucket' => 'bucket-name', 'LocationConstraint' => 'eu-west-1'])
            ->willThrow(S3Exception::class)
        ;

        $this->shouldThrow(CouldNotCreateBucket::class)->during('read', ['a/path']);
    }

    function it_throws_if_could_not_open(CommandInterface $command, $client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(true);
        $client->getCommand('GetObject', [
            'Bucket' => 'bucket-name',
            'Key'    => 'base/path/a/path',
            '@http'  => ['stream' => true]
        ])->willReturn($command);
        $client->execute($command)->willThrow(S3Exception::class);

        $file = $this->read('a/path');
        $file->shouldThrow(CouldNotOpen::class)->during('getSize');
    }

    function it_throws_if_could_not_read(CommandInterface $command, Stream $stream, $client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(true);
        $client->getCommand('GetObject', [
            'Bucket' => 'bucket-name',
            'Key'    => 'base/path/a/path',
            '@http'  => ['stream' => true]
        ])->willReturn($command);
        $client->execute($command)->willReturn(['Body' => $stream]);
        $stream->read(1024)->willReturn(implode(array_fill(0, 1024, 'a')), false);

        $file = $this->read('a/path');
        $file->shouldThrow(CouldNotRead::class)->during('getSize');
    }

    function it_throws_if_could_not_delete($client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(true);
        $client->deleteObject(['Bucket' => 'bucket-name', 'Key' => 'base/path/a/path'])->willThrow(S3Exception::class);

        $this->shouldThrow(CouldNotDelete::class)->during('delete', [new File('a/path', function () {})]);
    }

    function it_throws_if_could_not_list($client)
    {
        $client->doesBucketExist('bucket-name')->willReturn(true);
        $client
            ->getIterator('ListObjects', ['Bucket' => 'bucket-name', 'Prefix' => 'base/path/does/not/exists/'])
            ->willThrow(S3Exception::class)
        ;

        $generator = $this->find('does/not/exists/');
        $generator->shouldThrow(CouldNotList::class)->during('current');
    }
}
