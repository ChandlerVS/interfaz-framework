<?php declare(strict_types=1);

namespace DataHead\InterfazFramework;


use DataHead\InterfazFramework\ServiceProviders\FrameworkServiceProvider;
use Laminas\Diactoros\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Route\Router;
use Twig\Loader\FilesystemLoader;

class Framework
{
    static $instance = null;

    public $container;
    public $viewFolders;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function init() {

    }

    public function run() {
        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $response = $router->dispatch($this->container->get(ServerRequest::class));
        (new SapiEmitter())->emit($response);
    }

    static function getInstance(): Framework {
        if(self::$instance == null) {
            self::$instance = new Framework();
        }
        return self::$instance;
    }

    /**
     * @param $routes
     * Adds routes into the system from a given array
     */
    public function processRoutes($routes) {
        /** @var Router $router */
        $router = $this->container->get(Router::class);

        foreach ($routes as $route) {
            /** 0: Method | 1: The Route | 2: The Controller Method */
            $router->map($route[0], $route[1], $route[2]);
        }
    }
}
