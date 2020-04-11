<?php declare(strict_types=1);


namespace DataHead\InterfazFramework\ServiceProviders;


use DataHead\InterfazFramework\Framework;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

class FrameworkServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    protected $provides = [
        Router::class,
        ServerRequest::class,
        LoaderInterface::class,
        Environment::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getLeagueContainer()->share(Router::class);

        /** Add the views loader for Twig */
        $this->getLeagueContainer()->share(LoaderInterface::class, function() {
            return new FilesystemLoader(Framework::getInstance()->viewFolders);
        });
        $this->getLeagueContainer()->add(Environment::class)->addArgument(LoaderInterface::class);
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
