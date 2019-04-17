<?php

#判断是否是IS_POST请求
defined('IS_POST') OR define('IS_POST', strtoupper($_SERVER['REQUEST_METHOD'] == 'POST') ? TRUE : FALSE);
##判断是否是IS_AJAX请求
defined('IS_AJAX') OR define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false);
#判断是否是IS_GET请求
defined("IS_GET") OR define('IS_GET', strtoupper($_SERVER['REQUEST_METHOD'] == 'GET') ? TRUE : FALSE);


/**
 * 日志信息写入 json格式日志
 *
 * @param array|bool $logs
 * @return bool|int 写入日志长度
 */
function log_str($logs = false, $logFileName = 'history')
{
    if (empty($logs)) {
        return false;
    } else {

        $logsNew = [];
        foreach ($logs as $logsKey => $logsVal) {
            $logsNew[] = is_array($logsVal) || is_object($logsVal) ? "{$logsKey}:" . json_encode($logsVal, JSON_UNESCAPED_UNICODE) : $logsVal;
        }
        $logsStr = implode(' ', $logsNew);
        unset($logs);
        unset($logsNew);

        $log_month = date('Y_m');
        $logFileName = "{$logFileName}_" . date('Y_m_d') . '.log';

        $file_path = ROOT . "/logs/{$log_month}";
        mkdirs($file_path);

        $logTime = date('Y-m-d H:i:s');
        return file_put_contents("{$file_path}/{$logFileName}", "[{$logTime}] {$logsStr}\r\n", FILE_APPEND);

    }

}


/**
 * 目录递归创建
 *
 * @param $path
 * @return bool
 */
function mkdirs($path)
{
    if (is_dir($path)) {  //已经是目录了就不用创建
        return true;
    }
    if (is_dir(dirname($path))) {     //父目录已经存在，直接创建
        return mkdir($path, 0777);
    }
    mkdirs(dirname($path));     //从子目录往上创建
    return mkdir($path, 0777);    //因为有父目录，所以可以创建路径
}


//alert()函数
function alert($content = '')
{
    echo '<script>alert("' . $content . '")</script>';
}

//打印参数
function p($val = '', $type = false)
{
    echo '<pre>';
    if ($type || $val == false || @strlen($val) == 1) {
        var_dump($val);
    } else {
        print_r($val);
    }
}

//GET接值方法
function get($GET = false)
{
    if ($GET) {
        $GET = isset($_GET[$GET]) ? $_GET[$GET] : null;
    } else {
        $GET = isset($_GET) ? $_GET : null;
    }
    return xss($GET);
}

//POST接值方法
function post($POST = false)
{
    if ($POST) {
        $POST = isset($_POST[$POST]) ? $_POST[$POST] : null;
    } else {
        $POST = isset($_POST) ? $_POST : null;
    }
    return xss($POST);
}


//防xss攻击
function xss($VAL)
{
    $POST_NEW = [];
    if (!is_array($VAL)) {
        $POST_NEW = htmlspecialchars($VAL);
    } else {
        foreach ($VAL as $k => $v) {
            if (is_array($v)) {
                $POST_NEW[$k] = xss($v);
            } else {
                $POST_NEW[$k] = htmlspecialchars($v);
            }
        }
    }
    return $POST_NEW;
}

//数据库方法
function D($TableName = false)
{
    return new library\db\db($TableName);
}

//获取配置文件内容
function C($CONFIG_NAME = false)
{
    //获取框架配置信息
    $CONFIG = include ROOT . 'config/config.php';
    if ($CONFIG_NAME) {
        if (isset($CONFIG[$CONFIG_NAME])) {
            return $CONFIG[$CONFIG_NAME];
        } else {
            return false;
        }
    } else {
        return $CONFIG;
    }
}


//视图函数
function view($VIEW = '', $VALUES = false, $charset = 'utf-8', $contentType = 'text/html')
{
    //时光回溯-.-
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    unset($backtrace[0]);
    $BACKTRACE_DATA = array_pop($backtrace);
    //解析变量
    if ($VALUES) $VALUES = extract($VALUES);
    $VIEW = explode('/', trim($VIEW, '/'));
    $BACKTRACE_DATA_CLASS = explode('\\', $BACKTRACE_DATA['class']);
    //判断调用
    switch (count($VIEW)) {
        case 1;
            $ACTION_VIEW = empty($VIEW[0]) ? $BACKTRACE_DATA['function'] : $VIEW[0];
            $CONTROLLER_VIEW = array_pop($BACKTRACE_DATA_CLASS);
            break;
        default;
            $CONTROLLER_VIEW = empty($VIEW[0]) ? array_pop($BACKTRACE_DATA_CLASS) : $VIEW[0];
            $ACTION_VIEW = empty($VIEW[1]) ? $BACKTRACE_DATA['function'] : $VIEW[1];
            break;
    }
    //控制器目录
    $VIEW_FILE = ROOT . 'app/views/' . strtolower($CONTROLLER_VIEW) . '/' . strtolower($ACTION_VIEW) . '.php';
    // 网页字符编码
    header("content-type:{$contentType};charset={$charset}");
    if (is_file($VIEW_FILE)) {
        include_once $VIEW_FILE;
    } else {
        die("<h3>视图层文件不存在:</h3>$VIEW_FILE");
    }
}


//打开目标目录
function library($URL = false)
{
    $URL = VENDOR . 'library/' . $URL . '.php';
    if (is_file($URL)) {
        include_once $URL;
    } else {
        die("<h3>类库包文件不存在,路径:</h3>$URL");
    }

}

//cookie
function cookie($key = '', $value = '', $time = false)
{
    if ($key && $value) {
        if ($time) {
            setcookie($key, $value, $time, '/');
            return true;
        } else {
            setcookie($key, $value, 0, '/');
            return true;
        }
    } else {
        if ($key) {
            $cookie = isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
        } else {
            $cookie = $_COOKIE;
        }
        return xss($cookie);
    }
}


//销毁cookie
function destroy_cookie($key = '')
{
    if ($key) {
        setcookie($key, null, time() - 1, '/');
        unset($_COOKIE[$key]);
        return true;
    } else {
        foreach ($_COOKIE as $key => $val) {
            setcookie($key, '', time() - 3600, '/');
            unset($_COOKIE[$key]);
        }
        return true;
    }
}


//加密算法
function encode($string = '', $skey = 'jhj')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value) {
        $key < $strCount && $strArr[$key] .= $value;
    }
    return str_replace(array('=', '+', '/'), array('O0O0O', 'i000l', 'ooiio'), join('', $strArr));
}

//解密算法
function decode($string = '', $skey = 'jhj')
{
    $strArr = str_split(str_replace(array('O0O0O', 'i000l', 'ooiio'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value) {
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    }
    return base64_decode(join('', $strArr));
}

//查询Memcache缓存
function memc_get($key = '')
{
    return memc()->get($key);
}

//设置缓存
function memc_set($key = '', $val = '', $time = '')
{
    if ($time) {
        return memc()->set($key, $val, 0, $time);
    } else {
        return memc()->set($key, $val);
    }
}

//链接Memcache
function memc($host = '127.0.0.1', $port = 11211)
{
    $memcache = new Memcache();
    $memcache->connect($host, $port);
    return $memcache;
}


/**
 * Application.php
 *
 * 框架自带函数
 *
 * Copyright (c)2017 http://note.jsx6.com
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/

