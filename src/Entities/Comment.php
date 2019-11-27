<?php

namespace OCR5\Entities;

use DateTime;
use OCR5\Interfaces\EntityInterface;

class Comment implements EntityInterface
{
    private $id;
    private $post_id;
    private $name;
    private $email;
    private $content;
    private $status;
    private $added;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post_id;
    }

    /**
     * @param mixed $post
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     * @throws \Exception
     */
    public function getAdded()
    {
        return (new DateTime($this->added))->format('d/m/Y H:i');
    }

    /**
     * @param $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    public static function getRequestedTraduction()
    {
        return 'commentaire';
    }

    public static function getPublicFields()
    {
        return [
            'post' => 'Article',
            'name' => 'Nom',
            'content' => 'Message',
            'added' => 'Date d\'ajout'
        ];
    }
}