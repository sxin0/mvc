<?php


namespace app\middleware;


class controllers
{


    protected static $middleware = [
        auth::class     //权限
    ];


    /**
     * controllers constructor.
     *
     *
     * 加载中间层组件
     */
    public function __construct()
    {

        foreach (self::$middleware as $middlewareVal) {
            new $middlewareVal;
        }

    }


}





/**
 * controllers.php
 *
 * 说明:
 *
 * 修改历史
 * ----------------------------------------
 * 2019/4/17 20:50:00   操作:创建
 **/