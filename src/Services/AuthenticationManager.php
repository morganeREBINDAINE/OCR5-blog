<?php

namespace OCR5\Services;

use OCR5\Entities\User;

class AuthenticationManager extends Manager
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = $this->getHandler('user');
    }

    /**
     * Set a token for the session
     *
     * @param $user
     *
     * @return mixed
     */
    public function startSession($user)
    {
        $token = md5($user->getUsername() . mt_rand());
        $user->setToken(base64_encode($token));
        $_SESSION['user'] = $user;
        $_SESSION['user']->setHash(password_hash($user->getId() . $user->getRole() . $user->getId(), PASSWORD_DEFAULT));

        return $this->userRepository->saveToken($user->getUsername(), password_hash($token, PASSWORD_BCRYPT));
    }

    public function checkLogin($username, $password)
    {
        $user = $this->userRepository->getValid($username);

        return ($user !== false && password_verify($password, $user->getPassword())) ? $user : null;
    }

    /**
     * Compare token in session with the session one
     *
     * @return bool
     */
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

    /**
     * Ensure information stocked in client navigator have not been modified
     *
     * @return bool
     */
    public function ensureIdentity()
    {
        $sessionUser = $_SESSION['user'];

        $user = $this->userRepository->getValid($sessionUser->getUsername());

        return password_verify($user->getId() . $user->getRole() . $user->getId(), $sessionUser->getHash());
    }
}
