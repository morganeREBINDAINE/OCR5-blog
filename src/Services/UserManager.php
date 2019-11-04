<?php

namespace OCR5\Services;

class UserManager extends Manager
{
    public function findUser($attribute) {
        return $this->queryDatabase('SELECT * FROM user WHERE id = :attribute OR username = :attribute', [
            ':attribute' =>$attribute
        ], 'OCR5\Entities\User');
    }
}