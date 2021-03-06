<?php

class request
{
    public static $uri;
    public static $method;
    public static $arr_get_raw;
    public static $arr_get;
    public static $arr_post_raw;
    public static $arr_post;
    public static $arr_cookie_raw;
    public static $arr_cookie;
    public static $client_ip;
    public static $logid;

    public static function init()
    {
        self::$uri  = $_SERVER['REQUEST_URI'];
        self::$uri  = str_replace(land_conf::$web_root, "", self::$uri);
        self::$uri  = preg_replace("/\?.*/", "", self::$uri);
        if (self::$uri == "/") //主页模块为index
        {
            self::$uri = "/index";
        }
        self::$method           = $_SERVER['REQUEST_METHOD'];
        self::$arr_get_raw      = $_GET;
        self::$arr_get          = $_GET;
        self::$arr_post_raw     = $_POST;
        self::$arr_post         = $_POST;
        self::$arr_cookie_raw   = $_COOKIE;
        self::$arr_cookie       = $_COOKIE;
        if (get_magic_quotes_gpc())
        {
            apply_func_recursive("stripslashes", self::$arr_get);
            apply_func_recursive("stripslashes", self::$arr_post);
            apply_func_recursive("stripslashes", self::$arr_cookie);
        }

        self::$client_ip        = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            self::$client_ip    = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        self::$logid = crc32(microtime() . ip2long(self::$client_ip)) & 0x7FFFFFFF;
    }
}

?>
