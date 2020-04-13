<?php declare(strict_types=1);


namespace DataHead\InterfazFramework;

use DataHead\InterfazFramework\Session\SessionManager;
use Doctrine\ORM\EntityManager;

/**
 * Class AuthenticationManager
 * @package DataHead\InterfazFramework
 * Basic user authentication system. This requires the user model to have at least an email and a password.
 */
class AuthenticationManager
{
    protected $authenticatedUser;
    protected $userModel;

    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param $user
     * @throws \Exception
     */
    public function setAuthenticatedUser($user) {
        if($user instanceof $this->userModel) {
            $this->authenticatedUser = $user;
            $this->sessionManager->set('user_id', $user->getId());
        } else {
            throw new \Exception("Supplied user is not an instance of $this->userModel");
        }
    }

    public function setAuthenticatedUserById($id): bool {
        /** @var EntityManager $entityManager */
        $entityManager = Framework::getInstance()->container->get(EntityManager::class);
        $userRepo = $entityManager->getRepository($this->userModel);
        $user = $userRepo->find($id);

        if($user != null) {
            $this->setAuthenticatedUser($user);
            return true;
        } else {
            return false;
        }
    }

    public function attemptLogin($email, $password) {
        /** @var EntityManager $entityManager */
        $entityManager = Framework::getInstance()->container->get(EntityManager::class);
        $userRepo = $entityManager->getRepository($this->userModel);
        $user = $userRepo->findOneBy(['email' => $email]);
        if($user != null && password_verify($password, $user->getPassword())) {
            $this->setAuthenticatedUser($user);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $userModel
     */
    public function setUserModel($userModel): void
    {
        $this->userModel = $userModel;
    }

    /**
     * @return mixed
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }
}
