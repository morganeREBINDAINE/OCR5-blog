<?php

namespace OCR5\Services;

class AuthenticationManager extends Manager
{
    public function registerSession($username)
    {
        $_SESSION['username'] = $username;
        $token = md5($username . mt_rand());
        $_SESSION['token'] = base64_encode($token);

        return $this->saveToken($username, password_hash($token, PASSWORD_BCRYPT));
    }

    public function checkLogin($username, $password)
    {
        $result = $this->getPasswordByValidUsername($username);

        return password_verify($password, $result['password']);
    }

    private function saveToken($username, $token)
    {
        return $this->queryDatabase("UPDATE user SET token = :token WHERE username = :username", [
            ':username' => $username,
            ':token' => $token
        ]);
    }

    private function getPasswordByValidUsername($username)
    {
        return $this->queryDatabase("SELECT password FROM user WHERE username = :username AND status = 1", [':username' => $username]);
    }
}
