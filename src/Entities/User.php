<?php

namespace OCR5\Entities;

use DateTime;
use OCR5\Interfaces\EntityInterface;

class User implements EntityInterface
{
    private $id;
    private $username;
    private $email;
    private $password;
    private $hash;
    private $token;
    private $role;
    private $status;
    private $added;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAdded()
    {
        return (new DateTime($this->updated))->format('d/m/Y H:i');
    }

    /**
     * @param mixed $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    public static function getRequestedTraduction()
    {
        return 'rédacteur';
    }

    public static function getPublicFields()
    {
        return [
            'username' => 'Pseudo',
            'email' => 'Email',
            'added' => 'Date d\'ajout'
        ];
    }
}