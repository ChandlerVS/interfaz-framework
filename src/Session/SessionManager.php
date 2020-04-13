<?php

namespace DataHead\InterfazFramework\Session;

interface SessionManager
{
    public function initializeSession($sessionID = null): bool;
    public function get($key, $default = null);
    public function set($key, $value);
    public function delete($key);
}
