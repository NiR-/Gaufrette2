<?php

namespace features\Context;

interface Tester
{
    public function getFileSize(string $path): int;

    public function fileExists(string $path): bool;
}
