<?php

namespace Lib\Data;

class Query
{
    private $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function getResult()
    {
        $data = pg_fetch_all($this->resource);
        return empty($data) ? array() : $data;
    }
}