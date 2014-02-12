<?php

namespace Lib;

use Lib\Utility\Logger;

/**
 * Base processing for different media types
 */
abstract class Media extends Processable
{

    public final function process(XBMC $context)
    {
        Logger::log("Processing download: {$this->getFileName()}");

        try {
            $this->moveFiles();
            $this->clean();

            if ($this instanceof TVShow || $this instanceof Movie) {
                $context->setUpdateMovies(true);
            }

            if ($this instanceof Movie) {
                $context->setUpdateMusic(true);
            }

        } catch (\Exception $e) {
            Logger::log("Error : {$e->getMessage()}");
        }
    }

    protected abstract function moveFiles();
}

