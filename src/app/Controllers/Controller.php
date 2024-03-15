<?php

namespace Slim\Mpstats\Controllers;

use ClickHouseDB\Client;

class Controller
{
    protected ?Client $db = null;
    public function __construct($container)
    {
        if($container->has('db')){
            $this->db = $container->get('db');
        }
    }
}