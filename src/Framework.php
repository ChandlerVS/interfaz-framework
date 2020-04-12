<?php declare(strict_types=1);

namespace DataHead\InterfazFramework;


use DataHead\InterfazFramework\ServiceProviders\FrameworkServiceProvider;
use Dotenv\Dotenv;
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
    public $baseDir;

    /**
     * Framework constructor.
     * @param $baseDir string
     * The base directory is only required on the first request of the instance
     */
    public function __construct($baseDir)
    {
        $dotenv = Dotenv::createImmutable($baseDir);
        $dotenv->load();

        $this->container = new Container();
    }

    /**
     * Get the router, process the request and return the response
     */
    public function run(): void {
        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $response = $router->dispatch($this->container->get(ServerRequest::class));
        (new SapiEmitter())->emit($response);
    }

    /**
     * @param null $baseDir
     * @return Framework
     * The base directory is only required on the first request of the instance
     */
    static function getInstance($baseDir = null): Framework {
        if(self::$instance == null) {
            self::$instance = new Framework($baseDir);
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
