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
        return pg_fetch_all($this->resource);
    }
}