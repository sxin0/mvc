<?php


namespace app\model;


class indexModel
{

    public function index()
    {

        $db = D('good');

        return $db->insert(['class_name' => 'demo', 'age' => '123']);

    }


}





/**
 * indexModel.php
 *
 * 说明:
 *
 * 修改历史
 * ----------------------------------------
 * 2019/4/17 22:24:00   操作:创建
 **/