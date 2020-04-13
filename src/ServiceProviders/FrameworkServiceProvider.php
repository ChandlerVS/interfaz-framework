<?php declare(strict_types=1);


namespace DataHead\InterfazFramework\ServiceProviders;

use DataHead\InterfazFramework\Controller;
use DataHead\InterfazFramework\Framework;
use DataHead\InterfazFramework\Session\FileSystemSessionManager;
use DataHead\InterfazFramework\Session\SessionManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Router;
use DataHead\InterfazFramework\Middleware\SessionMiddleware;
use League\Route\Strategy\ApplicationStrategy;
use Symfony\Component\Console\Application;
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
        Configuration::class,
        EntityManager::class,
        Controller::class
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
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
        $this->getLeagueContainer()->share(ApplicationStrategy::class, function() {
            return (new ApplicationStrategy)->setContainer(Framework::getInstance()->container);
        });
        $this->getLeagueContainer()->share(Router::class, function() {
            return (new Router)->setStrategy($this->getLeagueContainer()->get(ApplicationStrategy::class));
        });

        if(php_sapi_name() !== 'cli') {
            $this->getContainer()->add(ServerRequest::class, function() {
                return ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
            });
        } else {
            $this->bootCli();
        }

        $this->getLeagueContainer()->share(Configuration::class, function() {
            /** @var Framework $framework */
            $framework = $this->getLeagueContainer()->get(Framework::class);
            return Setup::createAnnotationMetadataConfiguration($framework->doctrineSettings['paths'], $framework->doctrineSettings['dev_mode'], null, $framework->doctrineSettings['cache'], false);
        });
        $this->getLeagueContainer()->share(EntityManager::class, function() {
            /** @var Framework $framework */
            $framework = $this->getLeagueContainer()->get(Framework::class);
            $configuration = $this->getLeagueContainer()->get(Configuration::class);
            $conn = [
                'driver' => $framework->getenv('DOCTRINE_DRIVER', 'pdo_mysql'),
                'user' => $framework->getenv('DOCTRINE_DBUSER', 'root'),
                'password' => $framework->getenv('DOCTRINE_DBPASSWORD', ''),
                'host' => $framework->getenv('DOCTRINE_DBHOST', 'localhost'),
                'dbname' => $framework->getenv('DOCTRINE_DBNAME', 'interfaz')
            ];
            return EntityManager::create($conn, $configuration);
        });

        $this->getLeagueContainer()->share(SessionManager::class, function() {
            return new FileSystemSessionManager();
        });

        $this->baseMiddleware();
    }

    protected function bootCli() {
        $this->getLeagueContainer()->share(Application::class);
    }

    protected function baseMiddleware() {
        /** @var Router $router */
        $router = $this->getLeagueContainer()->get(Router::class);
        $router->middleware(new SessionMiddleware());
    }
}
