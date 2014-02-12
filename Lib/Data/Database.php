<?php

namespace Lib\Data;

use Lib\Utility\Configuration;

class Database
{
    private $connection;

    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $configuration = Configuration::getInstance();
            $instance = new Database($configuration->get('database/catalogue'), $configuration->get('database/user'));
        }
        return $instance;
    }

    private function __construct($catalogue, $userName)
    {
        $this->connection = pg_connect("dbname=$catalogue user=$userName");

        if (!$this->connection) {
            throw new \Exception('could not connect to database (is this file in the right directory)');
        }
    }

    public function query($query)
    {
        return new Query(pg_query($this->connection, $query));
    }
}

