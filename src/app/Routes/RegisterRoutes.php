<?php

namespace Slim\Mpstats\Routes;

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Mpstats\Controllers\Controller;
use Slim\Mpstats\Controllers\MigrationController;
use Slim\Mpstats\Controllers\ProductParseController;

class RegisterRoutes
{
    private App $app;
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {
        $this->set('/product/parse', [ProductParseController::class, 'parse']);
        $this->set('/product/get', [ProductParseController::class, 'get']);
        $this->set('/migration/migrate', [MigrationController::class, 'migrate']);
        $this->set('/migration/rollback', [MigrationController::class, 'rollback']);
    }

    private function set($url, $callable)
    {
        $this->app->get($url, function (Request $request, Response $response, array $args) use ($callable) {
            if(is_array($callable) && is_subclass_of($callable[0], Controller::class)){
                $callable[0] = new $callable[0]($this);
            }
            return $callable($request, $response, $args);
        });
    }
}