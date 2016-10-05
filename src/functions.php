<?php

namespace Gaufrette;

/**
 * Little wrapper around `dirname` ensuring all paths are UNIX-styles (even on Windows)
 *
 * @param string $path
 *
 * @return string
 */
function dirname($path) {
    return str_replace('\\', '/', \dirname($path));
}
