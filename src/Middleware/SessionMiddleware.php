<?php declare(strict_types=1);

namespace DataHead\InterfazFramework\Middleware;

use DataHead\InterfazFramework\Framework;
use DataHead\InterfazFramework\Session\Session;
use DataHead\InterfazFramework\Session\SessionManager;
use HansOtt\PSR7Cookies\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SessionMiddleware
 * @package DataHead\InterfazFramework\Middleware
 * This middleware checks for a session cookie and initializes the session accordingly
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();
        if(array_key_exists('session_token', $cookies)) {
            /** @var Session $sessionManager */
            $sessionManager = Framework::getInstance()->container->get(SessionManager::class);
            if($sessionManager->initializeSession($cookies['session_token']))
                return $handler->handle($request);
        }

        /** @var Session $sessionManager */
        $sessionManager = Framework::getInstance()->container->get(SessionManager::class);
        $sessionManager->initializeSession();

        $domain = Framework::getInstance()->getenv('DOMAIN');

        $response = $handler->handle($request);
        return $response->withAddedHeader('Set-Cookie', "session_token=$sessionManager->sessionID; SameSite=Strict; Domain=$domain; Path=/; SameSite=None");
    }
}
