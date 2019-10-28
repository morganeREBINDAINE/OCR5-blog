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

        $pdo->exec("CREATE DATABASE IF NOT EXISTS ocr5");
        $pdo->exec("use ocr5");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             username VARCHAR( 50 ) NOT NULL, 
             email VARCHAR( 255 ) NOT NULL,
             password VARCHAR( 255 ) NOT NULL,
             first_name VARCHAR( 255 ) NULL,
             last_name VARCHAR( 255 ) NULL,
             token VARCHAR( 255 ) NULL,
             role ENUM ('administrator', 'contributor') NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL,
             UNIQUE (username),
             UNIQUE (email)
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS comment (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             name VARCHAR( 50 ) NOT NULL, 
             email VARCHAR( 255 ) NOT NULL,
             content MEDIUMTEXT NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS article (
             id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             user_id INT( 11 ) NOT NULL, 
             title VARCHAR( 255 ) NOT NULL,
             content MEDIUMTEXT NOT NULL,
             excerpt MEDIUMTEXT NOT NULL,
             status TINYINT( 1 ) NOT NULL,
             added DATETIME NOT NULL,
             updated DATETIME NOT NULL,
             FOREIGN KEY (user_id) REFERENCES user(id)
        )");

        return $pdo;
    }

    public function query($query, $parameters = [], $multiple = false)
    {
        $statement = $this->pdo->prepare($query);
        $result = $statement->execute($parameters);

        if (substr($query, 0, 6) === "SELECT") {
            return ($multiple === true) ? $statement->fetchAll() : $statement->fetch();
        }

        return $result;
    }
}
