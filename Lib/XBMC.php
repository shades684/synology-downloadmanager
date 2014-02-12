<?php

namespace Lib;

use Lib\Utility\Configuration;
use Lib\Utility\Logger;
use Lib\Web\WebClient;

/**
 * Updates XBMC
 */
class XBMC
{
    private $updateMovies = false;
    private $updateMusic = false;

    /**
     * @param boolean $updateMovies
     */
    public function setUpdateMovies($updateMovies)
    {
        $this->updateMovies = $updateMovies;
    }

    /**
     * @param boolean $updateMusic
     */
    public function setUpdateMusic($updateMusic)
    {
        $this->updateMusic = $updateMusic;
    }

    public function update()
    {
        $configuration = Configuration::getInstance();

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

                if (!$this->updateMovies && !$this->updateMusic) {
                    Logger::log("No new media was added, skipping update process");
                }

                if ($this->updateMovies) {
                    $webClient->get($url . '/jsonrpc?request={"jsonrpc":"2.0","method":"VideoLibrary.Scan"}');
                }

                if ($this->updateMusic) {
                    $webClient->get($url . '/jsonrpc?request={"jsonrpc":"2.0","method":"AudioLibrary.Scan"}');
                }
            } catch (\Exception $e) {
                Logger::log('Error: fialed to update XBMC ' . $e->getMessage());
            }
        }
    }
} 