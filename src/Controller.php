<?php declare(strict_types=1);


namespace DataHead\InterfazFramework;


use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;

class Controller
{
    /** @var Environment */
    protected $twig;
    public function __construct()
    {
        $this->twig = Framework::getInstance()->container->get(Environment::class);
    }

    protected function view(string $view, array $variables = []): ResponseInterface {
        $result = $this->twig->render($view, $variables);
        return new HtmlResponse($result);
    }
}
