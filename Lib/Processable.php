<?php

namespace Lib;

use Lib\Utility\BigFileTools;
use Lib\Utility\Configuration;
use Lib\Utility\Logger;

/**
 * Base processing class
 */
abstract class Processable
{
    const TYPE_MOVIE = 1;
    const TYPE_TVSHOW = 2;
    const TYPE_MUSIC = 3;
    const TYPE_ARCHIVE = 4;

    private $videoExtensions;
    private $musicExtensions;
    private $archiveExtensions;

    /**
     * @var Download $download
     */
    private $download;
    private $originDirectory;
    private $fileName;
    private $userName;

    public function __construct()
    {
        $configuration = Configuration::getInstance();

        $this->musicExtensions = $configuration->get('extensions/music');
        $this->videoExtensions = $configuration->get('extensions/video');
        $this->archiveExtensions = $configuration->get('extensions/archive');
    }

    public static function getByDownload(Download $download)
    {
        $processable = self::getByDirectory($download->getDirectory());
        $processable->setDownload($download);

        return $processable;
    }

    public static function getByDirectory($directory)
    {
        Logger::log('Searching for directory ' . $directory);

        if (is_dir($directory)) {
            switch (self::getMediaType($directory)) {
                case self::TYPE_MOVIE :
                    return new Movie();
                    break;
                case self::TYPE_TVSHOW :
                    return new TVShow();
                    break;
                case self::TYPE_MUSIC :
                    return new Music();
                    break;
                case self::TYPE_ARCHIVE :
                    return new Archive();
                default:
                    throw new \Exception('Unknown media type');
            }
        }

        throw new \Exception('Directory not found');
    }

    private static function getMediaType($directory)
    {
        $configuration = Configuration::getInstance();
        $musicExtensions = $configuration->get('extensions/music');
        $videoExtensions = $configuration->get('extensions/video');
        $archiveExtensions = $configuration->get('extensions/archive');

        /**
         * There could be a movie and a rar with subtitles in the download directory, figure out what is important
         */
        $media = self::getMediaFile($directory, array_merge($musicExtensions, $videoExtensions));
        $archive = self::getMediaFile($directory, $archiveExtensions);

        if (is_null($archive)) {
            $fileName = $media;
        } elseif (!is_null($archive) && !is_null($media)) {
            $fMedia = new BigFileTools($media);
            $fArchive = new BigFileTools($archive);

            if ($fMedia->getSize() > $fArchive->getSize())
                $fileName = $media;
            else
                $fileName = $archive;
        } else {
            $fileName = $archive;
        }

        if (!is_null($fileName)) {
            $file = pathinfo($fileName);

            if (in_array($file['extension'], $musicExtensions))
                return self::TYPE_MUSIC;

            if (in_array($file['extension'], $archiveExtensions))
                return self::TYPE_ARCHIVE;

            if (in_array($file['extension'], $videoExtensions)) {
                if (preg_match("/^.*S([0-9]+)E([0-9]+).*$/i", $fileName)) {
                    return self::TYPE_TVSHOW;
                } else {
                    return self::TYPE_MOVIE;
                }
            }
        }

        throw new \Exception('Media type could not be determined');
    }

    private static function getMediaFile($directory, $extensions)
    {
        $contents = scandir($directory);
        foreach ($contents as $content) {
            if ($content != '.' && $content != '..') {
                $name = $directory . DIRECTORY_SEPARATOR . $content;

                if (!is_dir($name)) {
                    $file = pathinfo($name);
                    if (in_array($file['extension'], $extensions))
                        return $name;
                } else {
                    $file = self::getMediaFile($name, $extensions);
                    if (!is_null($file))
                        return $file;
                }
            }
        }

        return null;
    }

    public abstract function process(UpdateContext $context);

    protected function clean()
    {
        $configuration = Configuration::getInstance();

        if ($configuration->get('post-process/clean/files', false)) {
            Logger::log("Removing downloaded files");
            $this->delTree($this->getOriginDirectory());
        }

        $configuration = Configuration::getInstance();

        if ($configuration->get('post-process/clean/database', false) && !is_null($this->download)) {
            Logger::log("Removing download from database");
            $this->download->delete();
        }
    }

    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * @return string
     */
    public function getOriginDirectory()
    {
        return $this->originDirectory;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param \Lib\Download $download
     */
    private function setDownload($download)
    {
        $this->download = $download;
        $this->originDirectory = $download->getDirectory();
        $this->fileName = $download->getFileName();
        $this->userName = $download->getUserName();
    }

    /**
     * @param string $originDirectory
     */
    public function setOriginDirectory($originDirectory)
    {
        $this->originDirectory = $originDirectory;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getArchiveExtensions()
    {
        return $this->archiveExtensions;
    }

    /**
     * @return mixed
     */
    public function getMusicExtensions()
    {
        return $this->musicExtensions;
    }

    /**
     * @return mixed
     */
    public function getVideoExtensions()
    {
        return $this->videoExtensions;
    }

}

