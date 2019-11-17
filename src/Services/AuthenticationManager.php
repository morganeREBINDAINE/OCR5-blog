<?php

namespace OCR5\Services;

use OCR5\Entities\User;
use OCR5\Repository\UserRepository;

class AuthenticationManager extends Manager
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = $this->getRepository('user');
    }

    public function startSession($user)
    {
        $token = md5($user->getUsername() . mt_rand());
        $user->setToken(base64_encode($token));
        $_SESSION['user'] = $user;
        $_SESSION['user']->setHash(password_hash($user->getId() . $user->getRole() . $user->getId(), PASSWORD_DEFAULT));
//        die(var_dump($user));

        return $this->userRepository->saveToken($user->getUsername(), password_hash($token, PASSWORD_BCRYPT));
    }

    public function checkLogin($username, $password)
    {
        $user = $this->userRepository->getValid($username);

        return ($user !== false && password_verify($password, $user->getPassword())) ? $user : null;
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
        $dbUser = $this->userRepository->getValid($sessionUser->getUsername());

        if (false === password_verify($sessionToken, $dbUser->getToken())) {
            unset($_SESSION['user']);
            return false;
        }
        return true;
    }

    public function ensureIdentity()
    {
        $sessionUser = $_SESSION['user'];
        $backManager = new BackManager();
        $user = $backManager->getValid('user', $_SESSION['user']->getId());

        return password_verify($user->getId() . $user->getRole() . $user->getId(), $sessionUser->getHash());
    }

    private function saveToken($username, $token)
    {
        return $this->queryDatabase("UPDATE user SET token = :token WHERE username = :username", [
            ':username' => $username,
            ':token' => $token
        ]);
    }
}
