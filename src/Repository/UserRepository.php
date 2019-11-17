<?php

namespace OCR5\Repository;

class UserRepository extends Repository
{
    protected function getEntity()
    {
        return 'user';
    }

    public function getValids($limit = null, $offset = null, $condition = null)
    {
        $condition = $condition ? ' AND '.$condition : null;

        return parent::getValids($limit, $offset, 'role = "contributor"'.$condition);
    }

    public function getValid($username)
    {
        return $this->select('*', $this->getEntity(), 'status = 1 AND username = :username ', null, [
            ':username' => $username
        ]);
    }

    public function saveToken($username, $token)
    {
        return $this->update($this->getEntity(), 'token = :token', 'username = :username', [
            ':token' => $token,
            ':username' => $username,
        ]);
    }
}
