<?php

use ClickHouseDB\Client;
use Slim\Factory\AppFactory;
use DI\Container;

use Slim\Mpstats\Routes\RegisterRoutes;

require __DIR__ . '/vendor/autoload.php';


$container = new Container();

$container->set('db', function (){
    $config = [
        'host' => 'clickhouse-server',
        'port' => '8123',
        'username' => 'default',
        'password' => '',
        'https' => false
    ];

    $db = new Client($config);
    $db->database('default');
    return $db;
});

AppFactory::setContainer($container);
$app = AppFactory::create();


$app->addErrorMiddleware(true, false, false);

(new RegisterRoutes($app))->register();



$app->run();