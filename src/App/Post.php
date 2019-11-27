<?php

namespace OCR5\App;

class Post
{
    private static $post;
    private static $files;

    public static function start()
    {
        self::$post = &$_POST;
        self::$files = &$_FILES;
    }

    public static function get($key = null)
    {
        if ($key) {
            return (isset(self::$post[$key]) ? self::$post[$key] : null);
        }

        return self::$post;
    }

    public static function getFile($fileName)
    {
        return (isset(self::$files[$fileName]) ? self::$files[$fileName] : null);
    }
}
