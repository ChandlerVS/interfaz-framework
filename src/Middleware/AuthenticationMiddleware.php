<?php declare(strict_types=1);


namespace DataHead\InterfazFramework\Middleware;


use DataHead\InterfazFramework\AuthenticationManager;
use DataHead\InterfazFramework\Framework;
use DataHead\InterfazFramework\Session\SessionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var AuthenticationManager $authenticationManager */
        $authenticationManager = Framework::getInstance()->container->get(AuthenticationManager::class);
        /** @var SessionManager $sessionManager */
        $sessionManager = Framework::getInstance()->container->get(SessionManager::class);

        $userId = $sessionManager->get('user_id', false);
        if($userId) {
            $authenticationManager->setAuthenticatedUserById($userId);
        }
        return $handler->handle($request);
    }
}
