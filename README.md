# Gaufrette2

## What ?

A php7 library to represent files in different filesystems (aws S3, local, ftp, …).

## Why ?

The previous design is fundamentally flawed
concerning how it handles streams and big files.

This rewrite is a tentative to redesign the public API in a simpler manner,
using lazy, iterator based, filesystem-agnostic File value objects.

Using buffered streams effectively reduce the memory footprint from end to end.
Execution times are linear relative to content size.
Memory consumption is relative to the buffer size.

It also makes Directory first-class citizen.

## How ?

### Files

Files are represented by a path, a content and metadata.
The content has to be represented by a callable that returns an `\Iterator` of some sort.

```php
$file = new \Gaufrette\File('a/path', function(){
    for($i = 0; $i < 10000000; $i++) {
        yield 'abc';
    }
});
```

### Reading

```php
$fs = new Local\Filesystem('/base/path');
$fs->write($file);

$file = $fs->read('a/path');

```

### Writing

```php
$file = new \Gaufrette\File('a/path', function(){
    for($i = 0; $i < 10000000; $i++) {
        yield 'abc';
    }
});

$fs = new Local\Filesystem('/base/path');
$fs->write($file);
```

## Testing

```bash
docker-compose build
docker-compose run gaufrette phpspec run
docker-compose run gaufrette behat
```

## Adding support for a new filesystem

Implement the `Gaufrette\Filesystem` interface.

You can test your implementation against the generic feature suite by providing a behat's `ArgumentResolver` implementation
and register it in a test suite.

