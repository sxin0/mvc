<?php


namespace library\db;

class db extends \PDO
{
    //表名
    protected $Table_Name;
    //截取
    protected $Limit;
    //like
    protected $Like;
    //排序
    protected $Order;
    //join
    protected $Join;
    //sql语句
    protected $Sql;

    public function __construct($Table_Name)
    {
        $CONFIG = C();
        try
        {
            //初始化数据库
            parent::__construct("mysql:host={$CONFIG['host']};dbname={$CONFIG['dbname']};charset={$CONFIG['charset']}",$CONFIG['username'],$CONFIG['password']);
        }catch (\PDOException $e)//获取异常
        {
            //打印异常
            p($e->getMessage());die;
        }
        //再次设置数据库编码
        $this->exec("set names {$CONFIG['charset']}");
        $this->Table_Name = $Table_Name;
    }
    //修改
    public function update($where = false,$dataVal = false,$fetchSql = false)
    {
        $tableName = $this->Table_Name;
        $dataVal_d = ' set ';
        if(is_array($dataVal))
        {
            foreach ($dataVal as $k=>$v)
            {
                $dataVal_d .= '`'.$k.'`="'.$v.'",';
            }
        }else
        {
            $dataVal_d .= $dataVal;
        }

        $sql = 'update `'.$tableName.'`'.trim($dataVal_d,',').' where '.$where;
        //不执行,只获取sql
        if($fetchSql) die($sql);

        $MYSQL = $this->query($sql);
        if($MYSQL)
        {
            return $MYSQL;
        }else
        {
            /*p($this->errorInfo());
            echo $sql;*/
            return false;
        }
    }

    /**
     * 取得数据表的字段信息
     */
    public function getFields($tableName = false) {
        if (!$tableName) $tableName = $this->Table_Name;
        list($tableName) = explode(' ', $tableName);
        if(strpos($tableName,'.')){
            list($dbName,$tableName) = explode('.',$tableName);
            $sql   = 'SHOW COLUMNS FROM `'.$dbName.'`.`'.$tableName.'`';
        }else{
            $sql   = 'SHOW COLUMNS FROM `'.$tableName.'`';
        }
        $result = $this->query($sql);
        if($result)
        {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
        }else
        {
            p($this->errorInfo());die;
        }
        //p($result);die;
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }

    //添加一条
    public function insert($tableValue = false,$fetchSql = false)
    {
        $tableName = $this->Table_Name;
        //获取数据库字段
        $Table_Field = array_keys($this->getFields($tableName));
        $sql = 'insert into '.$tableName;
        $Table_Fields = '';
        $Table_Values = '';
        foreach ($tableValue as $k=>$v)
        {
            if(in_array($k,$Table_Field))
            {
                $Table_Fields .= "`$k`,";
                $Table_Values .= "'$v',";
            }
        }
        $sql .= '('.trim($Table_Fields,",").') values ('.trim($Table_Values,",").')';
        //不执行,只获取sql
        if($fetchSql) die($sql);
        $MYSQL = $this->exec($sql);
        if($MYSQL)
        {
            return $this->lastInsertId();
        }else
        {
            /*p($this->errorInfo());
            echo $sql;*/
            return false;
        }
    }

    //添加多条数据
    public function insertAll($tableValue = false,$fetchSql = false)
    {
        $tableName = $this->Table_Name;
        //获取数据库字段
        $Table_Field = array_keys($this->getFields($tableName));

        $sql = 'insert into '.$tableName;
        $Table_Values = '(';
        foreach ($tableValue as $v)
        {
            $Table_Fields = '';
            foreach ($v as $key=>$val)
            {
                if(in_array($key,$Table_Field))
                {
                    $Table_Fields .= "`$key`,";
                    $Table_Values .= "'$val',";
                }
            }
            $Table_Values = trim($Table_Values,',') .'),(';
        }
        $sql .= '('.trim($Table_Fields,",").') values '.substr($Table_Values,0,-2);
        //不执行,只获取sql
        if($fetchSql) die($sql);
        $MYSQL = $this->exec($sql);
        if($MYSQL)
        {
            return $this->lastInsertId();
        }else
        {
            /*p($this->errorInfo());
            echo $sql;*/
            return false;
        }

    }

    //删除
    public function delete($where = false,$fetchSql = false)
    {
        $tableName = $this->Table_Name;
        $sql = 'delete from '.$tableName.' where ';
        if(is_array($where))
        {
            $where_d = '';
            foreach ($where as $k=>$v)
            {
                $where_d .= '`'.$k.'`="'.$v.'" and';
            }
            $sql .= trim($where_d,'and');
        }else
        {
            $sql .= $where;
        }
        //不执行,只获取sql
        if($fetchSql) die($sql);

        $MYSQL = $this->exec($sql);
        if($MYSQL)
        {
            return $MYSQL;
        }else
        {
            /*p($this->errorInfo());
            echo $sql;*/
            return false;
        }
    }

    public function __call($name, $arguments)
    {
        $where = isset($arguments[0])?$arguments[0]:'';
        $field = isset($arguments[1])?$arguments[1]:'*';
        $fetchSql = isset($arguments[2])?$arguments[2]:false;
        switch ($name)
        {
            case 'one':
                return $this->_find($where,$field,$fetchSql,$num = 1);
            case 'select':
                return $this->_find($where,$field,$fetchSql,$num = 2);

        }
    }

    //查询
    protected function _find($where = '',$field = '*',$fetchSql = false,$num = 2)
    {
        $tableName = $this->Table_Name;
        $where_d = str_replace('`','',ltrim($this->Like,' and')).' '.$this->Order;
        if(str_replace(' ','',$where_d))
        {
            $where = ' where '.$where.' and '.$where_d;
        }else
        {
            if($where) $where = ' where '.$where;
        }
        $sql = 'select '.$field.' from '.$tableName.' '.ltrim($this->Join,' and ').$where.' '.$this->Limit;
        //不执行,只获取sql
        if($fetchSql) die($sql);

        $MYSQL = $this->query($sql);
        if($MYSQL)
        {
            if($num==1)
            {
                return $MYSQL->fetch(\PDO::FETCH_ASSOC);
            }else
            {
                return $MYSQL->fetchAll(\PDO::FETCH_ASSOC);
            }

        }else
        {
            return false;
        }
    }

    //连表查询
    public function join($join = '')
    {
        if($join)
        {
            $this->Join .= ' and '.$join;
        }
    }

    //like查询
    public function like($field = '',$where = '',$like = '%_%')
    {
        if($field)
        {
            $this->Like .= ' and `'.$field.'` like "'.str_replace('_',$where,$like).'"';
        }
    }

    //排序
    public function order_by($field = '',$desc = 'asc')
    {
        if($field)
        {
            $this->Order = 'order by '.$field.' '.$desc;
        }
    }

    //limit截取
    public function limit($limit_begin = '',$num = '')
    {
        $num = isset($num)?",$num":'';
        if($limit_begin)
        {
            $this->Limit = 'limit '.$limit_begin.$num.' ';
        }
    }

    /**
     * 获取数据库的表信息
     */
    public function getTables($dbName='') {
        if (!$dbName) $dbName = $this->Table_Name;
        $sql    = !empty($dbName)?'SHOW TABLES FROM '.$dbName:'SHOW TABLES ';
        $result = $this->query($sql);
        if($result)
        {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
        }else
        {
            p($this->errorInfo());die;
        }
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }


}











/**
 * db.php
 *
 * ...
 *
 * Copyright (c)2017 http://note.hanfu8.top
 *
 * 修改历史
 * ----------------------------------------
 * 2017/9/22, 作者: 降省心(QQ:1348550820), 操作:创建
 **/