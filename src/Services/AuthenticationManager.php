<?php

namespace OCR5\Services;

use OCR5\Entities\User;

class AuthenticationManager extends Manager

{
    public function startSession($username)
    {
        $user = $this->queryDatabase('SELECT * FROM user WHERE username = :attribute', [
            ':attribute' =>$username
        ], 'user');
        $token = md5($username . mt_rand());
        $user->setToken(base64_encode($token));
        $_SESSION['user'] = $user;
        $_SESSION['user']->setHash(password_hash($user->getId() . $user->getRole() . $user->getId(), PASSWORD_DEFAULT));

        return $this->saveToken($username, password_hash($token, PASSWORD_BCRYPT));
    }

    public function checkLogin($username, $password)
    {
        $result = $this->getPasswordByValidUsername($username);

        return password_verify($password, $result['password']);
    }

    public function compareTokens()
    {
        if (false === isset($_SESSION['user'])
            || false === $_SESSION['user'] instanceof User
        ) {
            return false;
        }
        $sessionUser = $_SESSION['user'];
        $sessionToken = base64_decode($sessionUser->getToken());
        $result = $this->queryDatabase('SELECT token FROM user WHERE id = :id', [
            ':id' => $sessionUser->getId()
        ]);

        if (false === password_verify($sessionToken, $result['token'])) {
            unset($_SESSION['user']);
            return false;
        }
        return true;
    }

    public function ensureIdentity() {
        $sessionUser = $_SESSION['user'];
        $backManager = new BackManager();
        $user = $backManager->getValid('user', $_SESSION['user']->getId());

        return password_verify($user->getId() . $user->getRole() . $user->getId(),$sessionUser->getHash());
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
