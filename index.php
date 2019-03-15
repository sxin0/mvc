<?php

//框架入口所在目录
define('ROOT',__DIR__.'/');
define('VENDOR',ROOT.'vendor/');

//加载框架配置
$CONFIG = include_once ROOT.'config/config.php';

//加载框架应用
include_once ROOT.'vendor/Application.php';
new vendor\Application($CONFIG);


/**
 * index.php
 *
 * 框架入口文件
 *
 * Copyright (c)2017 http://note.jsx6.com
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/
