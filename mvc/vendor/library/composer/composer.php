<?php

/*
 * 调用composer机制
 * */

//require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//临时
require_once 'autoload.php';

$whoops = new \Whoops\Run;
$option = new \Whoops\Handler\PrettyPageHandler();
$option->setPageTitle("代码出错");//title 标题
$whoops->pushHandler($option);
$whoops->register();




/******
 * User: 降省心
 * QQ:1348550820
 * Website: http://www.hanfu8.top
 * Date: 2017/8/4
 * Time: 8:44
 ******/
