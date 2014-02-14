<?php

namespace Lib;

/**
 * Parse music
 */
class Music extends Media
{
    protected function moveFiles()
    {
        throw new \Exception('Music not Implemented');
    }

    public function getTargetDirectory()
    {
        throw new \Exception('Target directory for Music not Implemented');
    }
}