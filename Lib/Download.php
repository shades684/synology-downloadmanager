<?php

namespace Lib;

use Lib\Data\Database;

/**
 * Get and removes completed downloads from the Synology database
 */
class Download
{
    const STATE_WAITING = 1;
    const STATE_ACTIVE = 2;
    const STATE_PAUSED = 3;
    const STATE_COMPLETING = 4;
    const STATE_COMPLETE = 5;
    const STATE_CHECKING = 6;
    const STATE_SEEDING = 8;
    const STATE_ERROR = 101;
    const STATE_TIMEOUT = 107;

    protected $id;
    protected $status;
    protected $destination;
    protected $fileName;
    protected $userName;
    protected $directory;

    /**
     * @return Download[]
     */
    public static function getCompleted()
    {
        $lst = array();

        $db = Database::getInstance();
        $query = $db->query("SELECT task_id, username, status, destination, filename FROM download_queue");

        foreach ($query->getResult() as $result) {
            if (intval($result['status']) == self::STATE_COMPLETE || intval($result['status']) == self::STATE_SEEDING)
                $lst[] = new Download(intval($result['task_id']), $result['username'], intval($result['status']), $result['destination'], $result['filename']);
        }

        return $lst;
    }

    public function delete()
    {
        if ($this->status == self::STATE_COMPLETE) {
            $db = Database::getInstance();
            $db->query('DELETE FROM download_queue WHERE task_id=' . $this->id);
        }
    }

    private function __construct($id, $userName, $status, $destination, $fileName)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->status = $status;
        $this->destination = $destination;
        $this->fileName = $fileName;
        $this->directory = $this->getTargetDirectory();
    }


    private function getTargetDirectory()
    {
        $prefix = 'home';

        if (substr($this->destination, 0, strlen($prefix)) == $prefix) {
            $homePath = $this->userName . substr($this->destination, strlen($prefix));

            return '/volume1/homes/' . $homePath . DIRECTORY_SEPARATOR . $this->fileName;
        } else {
            return '/volume1/' . $this->destination . DIRECTORY_SEPARATOR . $this->fileName;
        }
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
