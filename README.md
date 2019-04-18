## 框架部分说明

------

### 一、简单介绍：

**1. 异常处理**
![tool-editor](http://index.jsx6.com/image/error.png)

**2. api返回**
![tool-editor](http://index.jsx6.com/image/api.png)

**3. api异常日志记录**
![tool-editor](http://index.jsx6.com/image/apiError.png)

### 二、目录&&配置说明：

> * 所有参数配置文件都在config目录下面的config.php配置
> * app是项目目录
> * app/controllers 是控制器目录
> * app/service 是service目录（业务逻辑处理）
> * app/model 是model目录（数据库操作处理）
> * app/views 是视图目录（视图。。。）
> * vendor下面的Application.php 是核心
> * vendor/library 目录下面是自动加载的类库 第三方类库放这里
> * vendor/help 目录下面是帮助函数 可自己设置帮助函数


### 三、使用方式
**1. $_GET && $_POST**
```
    //GET接值
    $data = get();

    //POST接值
    $data = post();
```

**2. 打印数据**
```
    //加第二个参数,为打印详情; p($data,true);
    p($data);
```

**3. 接口异常抛出**
```
    throw new \Exception('exception', 403);
```

**4. 试图层**
```
    /*
     * 视图层调用,及传值
     * 第一个参数局默认为空的时候,默认调用当前控制器下的当前方法名文件
     * 传值以关联数组的形式
     * */
    view('',['name'=>'小明','age'=>13]);
    view('index');
    view('Index/index');
```

**5. 数据库操作**
```
    /*
     *******************************************************
     * 数据库操作 D()方法
     * 插入 insert()
     * 删除 delete()
     * 修改 update()
     * 查询 select()  查询单条 one()
     *******************************************************
     * */


    //添加 D('表名')->insert(['字段名'=>'值'],'获取sql语句的参数')
    //添加参数 1 返回获取sql语句
    //添加成功返回 主键id
    $id = D('cms')->insert(['name'=>'123','class'=>'123','app'=>'123']);
    $sql = D('cms')->insert(['name'=>'123','class'=>'123','app'=>'123'],1);
    p($id);
    die;


    //添加多条数据
    //添加 D('表名')->insertAll([['字段1'=>'值1'],['字段2'=>'值2']],'获取sql语句的参数')
    //添加参数 1 返回获取sql语句
    $value = [
        ['name'=>'123','class'=>'123','app'=>'123'],
        ['name'=>'122','class'=>'122','app'=>'122'],
        ['name'=>'121','class'=>'121','app'=>'121'],
    ];
    $reg = D('cms')->insertAll($value);
    $sql = D('cms')->insertAll($value,1);
    p($reg);


    //删除 delete from cms where `id`="1" and`username`="小"
    //返回影响的行数
    $reg = D('cms')->delete(['id'=>1,'username'=>'小']);
    //删除 delete from cms where id>125
    //where条件可以是字符串
    //返回影响的行数
    $reg = D('cms')->delete("id>125");


    //修改 条件 值
    //update cms set `name`="1",`class`="2" where id=125
    $reg = D('cms')->update('id=124',['name'=>1,'class'=>2]);


    #单条查询
    //one('条件','*')
    //one('条件','*',1) 返回sql语句
    $reg = D('cms')->one('id=111','name');
    p($reg);die;

    #查询全部
    //select('条件','查询的字段','是否输出')
    //select('条件','查询的字段')
    $db = D('cms');
    $db->join("inner join `cms_class` on cms.id=cms_class.id");
    $db->like("name",'','%_%');
    $db->like("cms.id",'1','%_%');
    $db->order_by('name');
    $db->limit(2,5);
    $data = $db->select('cms.id>10','*');

    p($data);die;


    //取得数据库的表信息
    //传入数据库名,返回该库下所有表名
    $table = D('js')->getTables();
    p($table);

    //取得数据表的字段信息
    $db = D('admin')->getFields();
    p($db);
```

**6. 类库引入**
```
    /*
     *********************************************
     * 引入第三方类库包
     * 第三方类库包放在 vendor/library 目录下面
     * 第三方类库包导入用 library();
     *********************************************
     * */
     
     
    /*
     * 例:PHPExcel表格导出类 的导入
     * PHPExcel导出 需要分别导入:PHPExcel IOFactory
     * 所以分别导入
     * */
    library('PHPExcel/PHPExcel');
    library('PHPExcel/PHPExcel/IOFactory');
    $excel = new \PHPExcel();
    $excel->setActiveSheetIndex()->setCellValueByColumnAndRow(0,1,"降省心");
    $excel->setActiveSheetIndex()->setCellValueByColumnAndRow(1,1,"框架");
    $excel->setActiveSheetIndex()->setCellValueByColumnAndRow(2,1,"测试");
    $excel = \IOFactory :: createWriter($excel, 'Excel5');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="框架类库包测试_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    $excel -> save('php://output');
```


**7. 其它**
```
    /*
     * 判断提交方式
     * is_ajax,is_get,is_post
     * */
    #判断是否是get提交
    $way = IS_GET;
    #判断是否是post提交
    $way = IS_POST;
    #判断是否是ajax提交
    $way = IS_AJAX;
    var_dump($way);


    /*
     * 获取配置信息
     * C();方法
     * */
    $c = C();
    p($c);die;
```

**欲知更多，请看源码🔍**
