<?php


namespace app\middleware;


class exceptions
{


    protected static $headers = [
        'Content-Type' => 'application/json;charset=utf-8',
        'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Cache-Control, Token',
        'Access-Control-Allow-Methods' => 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS, TRACE',
        'Access-Control-Allow-Origin' => '*'
    ];


    /**
     * 接口异常捕获
     *
     * @param $exception
     */
    public static function exceptionApi($exception)
    {

        //记录日常日志
        log_str([
            'exceptionApi',
            'code:' . $exception->getCode(),
            'file:' . $exception->getFile(),
            'line:' . $exception->getLine(),
            'message:' . $exception->getMessage()
        ]);
        if ($exception->getCode() != 0) {
            self::$headers['HTTP/1.1'] = $exception->getCode();
        }

        //返回异常原因
        self::returnResult([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ]);

    }


    /**
     * 设置返回header头
     */
    protected static function headerInit()
    {

        foreach (self::$headers as $headerKey => $headerVal) {
            header("{$headerKey}: {$headerVal}");
        }

    }


    /**
     * 正常接口返回处理
     *
     * @param $Result
     */
    public static function returnResult($Result)
    {

        self::headerInit();

        //非数组对象返回
        if (!is_array($Result) && !is_object($Result)) {
            exit($Result);
        }

        if (is_array($Result)) {
            $Result['meta'] = [
                'spentTime' => microtime(true) - MVC_START, //返回执行时间
                'memoryPeakUsage' => memory_get_peak_usage() . ' Byte'    //返回执行使用内存
            ];
        } else {
            $Result->meta = [
                'spentTime' => microtime(true) - MVC_START, //返回执行时间
                'memoryPeakUsage' => memory_get_peak_usage() . ' Byte'    //返回执行使用内存
            ];
        }

        exit(json_encode($Result, JSON_UNESCAPED_UNICODE));


    }


}





/**
 * exceptions.php
 *
 * 说明:
 *
 * 修改历史
 * ----------------------------------------
 * 2019/4/17 20:49:00   操作:创建
 **/