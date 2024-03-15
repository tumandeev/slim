<?php

namespace Slim\Mpstats\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MigrationController extends Controller
{
    public function migrate(Request $request, Response $response, array $args)
    {

        $this->db->write("
    CREATE TABLE IF NOT EXISTS product_collection (
        `ts` DateTime,
        `q` String,
        `id` Int64,
        `position` Int64
    )
    ENGINE=MergeTree()
    ORDER BY ts
");



        $response->getBody()->write(json_encode([
            'status' => true,
            'message' => "Migration successful completed",
        ]));

        return $response;
    }

    public function rollback(Request $request, Response $response, array $args)
    {
        $this->db->write('DROP TABLE IF EXISTS product_collection');

        $response->getBody()->write(json_encode([
            'status' => true,
            'message' => "Successful migration rollback",
        ]));

        return $response;
    }
}