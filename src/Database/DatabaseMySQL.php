<?php

namespace OCR5\Database;

use PDO;

class DatabaseMySQL
{
    public $pdo;

    public function __construct()
    {
        $this->pdo = $this->initDatabase();
    }

    private function initDatabase()
    {
        $pdo = new PDO('mysql:host=localhost;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $this->databaseName);
        $pdo->exec("use " . $this->databaseName);

        $pdo->exec("CREATE TABLE IF NOT EXISTS user (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             username VARCHAR( 50 ) NOT NULL, 
             email VARCHAR( 255 ) NOT NULL,
             password VARCHAR( 255 ) NOT NULL,
             token VARCHAR( 255 ) NULL,
             role ENUM ('administrator', 'contributor') NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL,
             UNIQUE (username),
             UNIQUE (email)
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS comment (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             post_id INT NOT NULL,
             name VARCHAR( 50 ) NOT NULL, 
             email VARCHAR( 255 ) NOT NULL,
             content MEDIUMTEXT NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL,
             FOREIGN KEY (post_id) REFERENCES post(id)
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS post (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             user_id INT( 11 ) NOT NULL, 
             title VARCHAR( 255 ) NOT NULL,
             content MEDIUMTEXT NOT NULL,
             chapo MEDIUMTEXT NOT NULL,
             image VARCHAR( 255 ) NOT NULL,
             extension VARCHAR( 255 ) NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL,
             updated DATETIME NULL,
             FOREIGN KEY (user_id) REFERENCES user(id)
        )");

        return $pdo;
    }

    public function query($query, $parameters = [], $className = null, $multiple = false)
    {
//        var_dump($query);
        $statement = $this->pdo->prepare($query);
        $result = $statement->execute($parameters);


        if (substr($query, 0, 6) === "SELECT") {
            if (class_exists($className)) {
                $statement->setFetchMode(PDO::FETCH_CLASS, $className);
            }
            return ($multiple === true) ? $statement->fetchAll() : $statement->fetch();
        }

        return $result;
    }
}
