<?php

namespace app\controllers;


use app\middleware\controllers;
use app\service\indexService;

class index extends controllers
{


    /**
     * 异常返回
     *
     * @throws \Exception
     */
    public function apiError()
    {

        (new indexService())->apiError();

    }


    /**
     * 正常接口返回
     *
     * @return array
     */
    public function api()
    {

        return (new indexService())->api();


    }


    /**
     * view层
     */
    public function index()
    {

        $reg = (new indexService())->index();

        //p($data);die;
        view('', ['key' => '小明', 'reg' => $reg]);
    }
}











/**
 * index.php
 *
 * ...
 *
 * Copyright (c)2017 http://note.jsx6.com
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/