<?php declare(strict_types=1);

namespace DataHead\InterfazFramework\Session;

use Ramsey\Uuid\Uuid;

class Session implements SessionManager
{
    public $name = "session";
    public $sessionID;

    public function initializeSession($sessionID = null): bool {
        return false;
    }

    protected function generateSessionID(): string {
        return Uuid::uuid4()->toString();
    }

    public function get($key, $default = null)
    {
        // TODO: Implement get() method.
    }

    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }
}
