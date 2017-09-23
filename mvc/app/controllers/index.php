<?php
namespace app\controllers;


class index
{
    public function index()
    {
        $db = D('good');
        $db->insert(['class_name'=>'小明','age'=>'123']);
        //p($data);die;
        view('',['key'=>'小明']);
    }
}











/**
 * index.php
 *
 * ...
 *
 * Copyright (c)2017 http://note.hanfu8.top
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/