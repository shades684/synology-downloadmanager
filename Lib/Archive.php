<?php

namespace Lib;

/**
 * Unpacks an archive and creates a new Processable from the target directory (which needs to be processed)
 */
class Archive extends Processable
{
    public function process(XBMC $context)
    {
        //unpack
        //new processable::getByDirectory(targetDirectory)
        //processable->setUserName
        //processable->setFileName
        //processable->setOrginDirectory (unpack directory)
        //$processable->process($context);
        //$this->clean();

        throw new \Exception('Archive not Implemented');
    }
}