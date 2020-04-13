<?php declare(strict_types=1);

namespace DataHead\InterfazFramework\Session;

final class FileSystemSessionManager extends Session
{
    public $name = "filesystem";
    private $storageDirectory;
    private $session;

    public function initializeSession($sessionID = null): bool
    {
        if($sessionID == null)
            $this->sessionID = $this->generateSessionID();
        else {
            if(file_exists($this->storageDirectory . "/$sessionID.json"))
                $this->sessionID = $sessionID;
            else
                return false;
        }
        $this->initializeSessionFile();
        return true;
    }

    private function initializeSessionFile() {
        if(file_exists($this->getFilePath())) {
            $this->session = json_decode(file_get_contents($this->getFilePath()), true);
        } else {
            $this->session = ['session_id' => $this->sessionID];
            $this->updateSessionFile();
        }
    }

    private function getFilePath() {
        return $this->storageDirectory . "/$this->sessionID.json";
    }

    private function updateSessionFile() {
        file_put_contents($this->getFilePath(), json_encode($this->session, JSON_PRETTY_PRINT));
    }

    public function get($key, $default = null)
    {
        if(array_key_exists($key, $this->session)) {
            return $this->session[$key];
        } else {
            return $default;
        }
    }

    public function set($key, $value)
    {
        $this->session[$key] = $value;
        $this->updateSessionFile();
    }

    public function delete($key)
    {
        if(array_key_exists($key, $this->session)) {
            unset($this->session[$key]);
        }
        $this->updateSessionFile();
    }

    /**
     * @param string $storageDirectory
     */
    public function setStorageDirectory(string $storageDirectory): void
    {
        $this->storageDirectory = $storageDirectory;
    }
}
