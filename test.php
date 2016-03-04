<?php

require __DIR__.'/vendor/autoload.php';

use Gaufrette\Filesystem\Local;
use Gaufrette\File;


$local = new Local;

$local->write(new File('a/path', function() use ($argv) {
    for($i = 0; $i < $argv[1]; $i++) {
        yield 'abc';
    }
}));
