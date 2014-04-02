<?php

namespace Lib;

use Lib\Utility\BigFileTools;
use Lib\Utility\Configuration;
use Lib\Utility\Logger;

/**
 * Parse movie
 */
class Movie extends Media
{
    private $specialWords = array('a', 'an', 'and', 'the', 'in', 'of');

    protected function moveFiles()
    {
        $directoryName = $this->getTargetDirectory();
        Logger::log("Parsed torrent, decided on directory: {$directoryName}");

        $fileOrigin = $this->getFile();
        Logger::log("Parsed files, decided on file: {$fileOrigin}");
        $fileTarget = $directoryName . DIRECTORY_SEPARATOR . basename($fileOrigin);

        if (!is_dir($directoryName)) {
            Logger::log("Creating directory: {$directoryName}");
            mkdir($directoryName);

            umask(0);
            chmod($directoryName, 0777);
            chown($directoryName, $this->getUserName());
            chgrp($directoryName, 'users');

            Logger::log("Copying file: {$fileOrigin} to {$fileTarget}");
            system("cp " . escapeshellarg($fileOrigin) . " " . escapeshellarg($fileTarget));
            chown($fileTarget, $this->getUserName());
            chgrp($fileTarget, 'users');
        } else {
            throw new \Exception('Directory already exists ' . $directoryName);
        }
    }

    public function getTargetDirectory()
    {
        $configuration = Configuration::getInstance();

        preg_match('/^(.*?)[\W]+([0-9]{4}).*$/', $this->getFileName(), $matches);

        if (sizeof($matches) < 3) {
            throw new \Exception('Something went wrong with creating the filename for ' . $this->getFileName());
        }

        $words = preg_split('/[\+ \.](?![\+ \.])/', $matches[1]);
        $words[0] = ucfirst($words[0]);

        for ($i = 1; $i < sizeof($words); $i++) {
            if (in_array(strtolower($words[$i]), $this->specialWords)) {
                $words[$i] = strtolower($words[$i]);
            }
        }

        return $configuration->get('target/movie') . '/' . implode(' ', $words) . " ($matches[2])";
    }

    private function getFile()
    {
        $fileName = null;
        $fileSize = 0;

        foreach ($this->getFiles($this->getOriginDirectory()) as $file) {
            $fs = new BigFileTools($file);
            $size = $fs->getSize();

            if ($size > $fileSize) {
                $fileName = $file;
                $fileSize = $size;
            }
        }

        return $fileName;
    }

    private function getFiles($directory)
    {
        $result = array();

        $contents = scandir($directory);
        foreach ($contents as $content) {
            if ($content != '.' && $content != '..') {
                $name = $directory . DIRECTORY_SEPARATOR . $content;

                if (!is_dir($name)) {
                    $file = pathinfo($name);
                    if (in_array($file['extension'], $this->getVideoExtensions()))
                        $result[] = $name;
                } else {
                    foreach ($this->getFiles($name) as $file) {
                        $result[] = $file;
                    }
                }
            }
        }

        return $result;
    }
}
