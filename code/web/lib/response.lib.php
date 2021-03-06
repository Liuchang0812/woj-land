<?php

class response
{
    public static $arr_headers = array(
        "Content-Type: text/html; charset=utf-8",
        );
    public static $arr_data    = array();

    public static $tpl_file;

    public static function add_header($head_str)
    {
        array_push(self::$arr_headers, $head_str);
    }

    public static function send_headers()
    {
        foreach (self::$arr_headers as $head_str)
        {
            $headstr = trim($head_str);
            header($head_str);
        }
    }

    public static function set_data_arr($arr_data)
    {
        if (is_array($arr_data))
        {
            self::$arr_data = $arr_data;
        }
    }

    public static function add_data($key, $value)
    {
        self::$arr_data[$key] = $value;
    }

    public static function add_data_arr($arr_data)
    {
        if (is_array($arr_data))
        {
            self::$arr_data = array_merge(self::$arr_data, $arr_data);
        }
    }

    public static function set_redirect($url)
    {
        ob_clean();
        //增加这一段是为了保证不会重定向到未知网站，以免被利用
        $arr_url = parse_url($url);
        $url = $arr_url['path'];
        if (isset($arr_url['query'])) $url .= '?' . $arr_url['query'];
        if (isset($arr_url['fragment'])) $url .= '#' . $arr_url['fragment'];
        //FM_LOG_DEBUG("redirect to: %s", $url);

        header("HTTP/1.1 301 Moved Permanently");
        header("location: " . $url);
        exit();
    }

    public static function set_tpl($tpl_file)
    {
        self::$tpl_file = $tpl_file;
    }

    public static function display()
    {
        db_close_all();
        self::send_headers();
        if (land_conf::DEBUG)
        {
            FM_LOG_DEBUG("arr_data: %s", print_r(self::$arr_data, true));
        }
        if (empty(self::$tpl_file))
        {
            self::$tpl_file = LIB_ROOT . "/template/default_tpl.lib.php";
        }
        ctemplate_run::run(self::$tpl_file, land_conf::DEFAULT_TPL_CLASS);
    }

    public static function display_msg($msg)
    {
        self::add_data('msg', $msg);
        if (!isset(self::$arr_data['links']['Go Back']))
        {
            self::add_link('Go Back', 'javascript:history.back(1)');
        }
        self::set_tpl(TPL_ROOT . '/msg.tpl.php');
    }

    public static function display_err($msg, $do_exit = true)
    {
        self::add_header('HTTP/1.1 403 Bad Request');
        self::add_data('errmsg', $msg);
        self::add_link('Go Back', 'javascript:history.back(1)');
        self::set_tpl(TPL_ROOT . '/error.tpl.php');
        self::display();
        if ($do_exit) 
            exit();
    }

    public static function add_links($arr_links)
    {
        if (isset(self::$arr_data['links']))
        {
            $arr_links = array_merge(self::$arr_data['links'], $arr_links);
        }
        self::$arr_data['links'] = $arr_links;
    }

    public static function add_link($name, $link)
    {
        self::add_links(array($name => $link));
    }
}

?>
