<?php


namespace app\service;


use app\model\indexModel;

class indexService
{


    public function index()
    {

        //echo 'error'

        return (new indexModel())->index();
    }


    public function api()
    {

        return [
            'message' => 'test'
        ];

    }


    public function apiError()
    {
        throw new \Exception('exception', 403);
    }


}





/**
 * indexService.php
 *
 * 说明:
 *
 * 修改历史
 * ----------------------------------------
 * 2019/4/17 22:24:00   操作:创建
 **/