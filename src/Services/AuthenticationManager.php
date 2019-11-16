<?php

namespace OCR5\Services;

class AuthenticationManager
{
    public function startSession($username)
    {
        $user = $this->queryDatabase('SELECT * FROM user WHERE username = :attribute', [
            ':attribute' =>$username
        ], 'OCR5\Entities\User');
        $token = md5($username . mt_rand());
        $user->setToken(base64_encode($token));
        $_SESSION['user'] = $user;

        return $this->saveToken($username, password_hash($token, PASSWORD_BCRYPT));
    }

    public function checkLogin($username, $password)
    {
        $result = $this->getPasswordByValidUsername($username);

        return password_verify($password, $result['password']);
    }

    public function compareTokens($sessionUser) {
        $sessionToken = base64_decode($sessionUser->getToken());
        $result = $this->queryDatabase('SELECT token FROM user WHERE id = :id', [
            ':id' => $sessionUser->getId()
        ]);

        return password_verify($sessionToken, $result['token']);
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
