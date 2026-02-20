<?php
/**
 *Session Class
 **/
class Session
{
    public static function init()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            if (session_id() == '') {
                session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
    }

    public static function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    public static function checkValid($which)
    {
        //self::init();
        // if (self::get($which."ID") == false) {
        //     self::destroy();
        // }
    }

    public static function checkLogin()
    {
        // self::init();
        // if (self::get("adminlogin") == true) {
        //     header("Location:dashboard.php");
        // }
    }

    public static function destroy()
    {
        session_destroy();
        header("Location:index.php");
        exit();
    }
    public static function logout(){
        session_start();
        session_destroy();
        header("Location:home.php");
        exit();
    }
}
