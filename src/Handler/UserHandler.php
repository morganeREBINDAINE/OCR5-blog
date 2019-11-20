<?php

namespace OCR5\Handler;

class UserHandler extends Handler
{
    protected function getEntity()
    {
        return 'user';
    }

    public function create($formData)
    {
        return $this->queryDatabase("INSERT INTO user (username, email, password, role, status, added) VALUES (:username, :email, :password, 'contributor', 0, NOW())", [
            ':username' => $formData['username'],
            ':email' => $formData['email'],
            ':password' => password_hash($formData['password'], PASSWORD_DEFAULT),
        ]);
    }

    public function getValids($limit = null, $offset = null, $condition = null, $join = null)
    {
        $condition = $condition ? ' AND '.$condition : null;

        return parent::getValids('role = "contributor"'.$condition, $join, $limit, $offset);
    }

    public function getValid($username)
    {
        return $this->select('*', $this->table, 'u.status = 1 AND u.username = :username ', null, [
            ':username' => $username
        ]);
    }

    public function saveToken($username, $token)
    {
        return $this->update($this->table, 'token = :token', 'username = :username', [
            ':token' => $token,
            ':username' => $username,
        ]);
    }
}
