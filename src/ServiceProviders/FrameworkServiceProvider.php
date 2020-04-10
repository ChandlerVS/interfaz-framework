<?php declare(strict_types=1);


namespace DataHead\InterfazFramework\ServiceProviders;


use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Router;

class FrameworkServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    protected $provides = [
        Router::class
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getContainer()->add(Router::class)->setShared();
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        $this->getContainer()->add(ServerRequest::class, function() {
            return ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        });
    }
}
