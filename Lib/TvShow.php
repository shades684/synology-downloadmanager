<?php

namespace Lib;

/**
 * Parse TVShow
 */
class TVShow extends Media
{
    protected function moveFiles()
    {
        throw new \Exception('TVShow not Implemented');
    }

    public function getTargetDirectory()
    {
        throw new \Exception('Target directory for TVShow not Implemented');
    }
}