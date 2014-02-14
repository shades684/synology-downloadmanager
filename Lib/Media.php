<?php

namespace Lib;

use Lib\Utility\Logger;

/**
 * Base processing for different media types
 */
abstract class Media extends Processable
{
    protected abstract function moveFiles();

    public abstract function getTargetDirectory();

    public final function process(UpdateContext $context)
    {
        Logger::log("Processing download: {$this->getFileName()}");

        try {
            $this->moveFiles();
            $this->clean();
            $context->addMedia($this);

        } catch (\Exception $e) {
            Logger::log("Error : {$e->getMessage()}");
        }
    }
}

