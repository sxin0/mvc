<?php

namespace vendor;

use app\middleware\exceptions;

class Application
{
    //配置参数
    private $CONFIG;
    //已加载的类
    private $PARAMETER = [];

    //构造方法
    public function __construct($CONFIG)
    {
        $this->CONFIG = $CONFIG;
        //加载自定义函数
        spl_autoload_register([$this, 'aotoload']);
        $this->DeBug();
        //加载自定义方法
        $this->HELP();
        $this->Route();
    }

    //加载自定义方法
    protected function HELP()
    {
        foreach ($this->CONFIG['HELP'] as $V) {
            //加载自定义函数库
            include_once VENDOR . 'help/' . $V . '.php';
        }
    }

    //路由
    protected function Route()
    {
        $ROUTE = trim(isset($_GET['r']) ? $_GET['r'] : '', '/');
        unset($_GET['r']);
        $ROUTE_NEW = explode('/', $ROUTE);
        //控制器
        $CONTROLLER = "app\controllers\\" . strtolower(empty($ROUTE_NEW[0]) ? $this->CONFIG['DefaultController'] : $ROUTE_NEW[0]);
        //方法
        $ACTION = isset($ROUTE_NEW[1]) ? $ROUTE_NEW[1] : $this->CONFIG['DefaultAction'];

        try {
            $result = (new $CONTROLLER)->$ACTION();
            exceptions::returnResult($result);
        } catch (\Exception $exception) {
            exceptions::exceptionApi($exception);
        }
    }

    //报错规则
    public function DeBug()
    {
        //判断是否生成错误日志
        if ($this->CONFIG['DeBug']) {
            //加载composer报错类库
            include_once VENDOR . 'library/composer/composer.php';;
        } else {
            //判断是否需要创建文件
            $Debug_file = ROOT . 'logs/' . date('Y_m_d', time()) . '.log';
            if (!is_file($Debug_file)) {
                fopen($Debug_file, 'w');
            }
            // 报告所有 PHP 错误
            error_reporting(E_ALL);
            // 不显示满足上条 指令所定义规则的所有错误报告
            ini_set('display_errors', 'Off');
            //开启错误日志 决定日志语句记录的位置
            ini_set('log_errors', 'On');
            //设置每个日志项的最大长度
            ini_set('log_errors_max_len', 1024);
            //设置日志路径 指定产生的 错误报告写入的日志文件位置
            ini_set('error_log', $Debug_file);
        }
    }

    //自动加载的类
    protected function aotoload($CLASS)
    {
        if (in_array($CLASS, $this->PARAMETER)) {
            return true;
        } else {
            $AUTOLOAD = str_replace('\\', '/', $CLASS) . '.php';
            $AUTOLOAD_DATA = explode('/', $AUTOLOAD);
            //判断要加载文件类型
            switch ($AUTOLOAD_DATA[0]) {
                case 'library';
                    $AUTOLOAD_FILE = VENDOR . $AUTOLOAD;
                    break;
                case 'app';
                    $AUTOLOAD_FILE = ROOT . $AUTOLOAD;
                    break;
            }
            if (isset($AUTOLOAD_FILE)) {
                if (is_file($AUTOLOAD_FILE)) {
                    include_once $AUTOLOAD_FILE;
                    $this->PARAMETER[$CLASS] = $CLASS;
                } else {
                    return false;
                }
            }
        }
    }

}









/**
 * Application.php
 *
 * 框架应用
 *
 * Copyright (c)2017 http://note.jsx6.com
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/
