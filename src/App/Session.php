<?php

namespace OCR5\App;

class Session
{
    private static $vars;

    public static function set($key, $value, $key2 = null)
    {
        $key2 ? self::$vars[$key][$key2] = $value : self::$vars[$key] = $value;
    }

    public static function get($key = null)
    {
        if ($key) {
            return (isset(self::$vars[$key]) ? self::$vars[$key] : null);
        }

        return self::$vars;
    }

    public static function unset($key)
    {
        unset(self::$vars[$key]);
    }

    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
            self::$vars = &$_SESSION;
        }
    }

    public static function destroy()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
