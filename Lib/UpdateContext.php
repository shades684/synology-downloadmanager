<?php

namespace Lib;

use Lib\Utility\Configuration;
use Lib\Utility\Logger;
use Lib\Web\WebClient;

/**
 * Updates XBMC and Indexing of Synology
 */
class UpdateContext
{
    /**
     * @var Media[] $medias
     */
    protected $medias = array();

    public function addMedia(Media $media)
    {
        $this->medias[] = $media;
    }

    public function update()
    {
        $updateMovies = false;
        $updateMusic = false;

        $configuration = Configuration::getInstance();

        foreach ($this->medias as $media) {

            system('synoindex -A ' . escapeshellarg($media->getTargetDirectory()));

            if ($media instanceof TVShow || $media instanceof Movie) {
                $updateMovies = true;
            }

            if ($media instanceof Music) {
                $updateMusic = true;
            }
        }

        if ($configuration->get('post-process/xbmc/enabled', false)) {

            try {
                Logger::log('Notifying XBMC for updates');

                $webClient = new WebClient();

                $user = $configuration->get('post-process/xbmc/user');
                $password = $configuration->get('post-process/xbmc/password');
                $hostname = $configuration->get('post-process/xbmc/hostname');
                $port = $configuration->get('post-process/xbmc/port');

                $url = "http://{$user}:{$password}@{$hostname}:{$port}";

                Logger::log("Notifying on $url");

                if (!$updateMovies && !$updateMusic) {
                    Logger::log("No new media was added, skipping update process");
                }

                if ($updateMovies) {
                    $webClient->get($url . '/jsonrpc?request={"jsonrpc":"2.0","method":"VideoLibrary.Scan"}');
                }

                if ($updateMusic) {
                    $webClient->get($url . '/jsonrpc?request={"jsonrpc":"2.0","method":"AudioLibrary.Scan"}');
                }
            } catch (\Exception $e) {
                Logger::log('Error: fialed to update XBMC ' . $e->getMessage());
            }
        }
    }
} 