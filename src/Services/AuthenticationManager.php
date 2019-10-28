<?php

namespace OCR5\Services;

class AuthenticationManager
{
    public function registerSession($username)
    {
        $_SESSION['username'] = $username;
        $token = md5($username . mt_rand());
        $_SESSION['token'] = base64_encode($token);

        $databaseManager = new DatabaseManager();
        $databaseManager->saveToken($username, password_hash($token, PASSWORD_BCRYPT));
    }
}
