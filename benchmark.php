<?php

require __DIR__.'/vendor/autoload.php';

use Gaufrette\File;
use Gaufrette\Filesystem;

$map = [
    'local' => Filesystem\Local\Filesystem::class,
    's3'    => Filesystem\AwsS3\Filesystem::class,
];

if ($argc !== 3) {
    echo 'Usage: filesystem iterations chunkSize';
    echo 'Filesystems: '.implode(' ', array_keys($map));
    exit(1);
}

/** @var Filesystem $filesystem */
$filesystem = new $map[$argv[1]];

$filesystem->write(new File('a/path', function() use ($argv) {
    for($i = 0; $i < $argv[2]; $i++) {
        yield implode(array_fill(0, $argv[3], 'a'));
    }
}));
