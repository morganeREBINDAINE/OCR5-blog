<?php

namespace OCR5\Services;

use OCR5\App\AppManager;

class DatabaseManager
{
    private $db;

    public function __construct()
    {
        $this->db = AppManager::getDatabase();
    }

    public function checkLogin($username, $password) {
        $result = $this->db->query("SELECT password FROM user WHERE username = :username", [':username' => $username]);

        return password_verify($password, $result['password']);
    }

    public function saveToken($username, $token) {
        return $this->db->query("UPDATE user SET token = :token WHERE username = :username", [
            ':username' => $username,
            ':token' => $token
        ]);
    }
}