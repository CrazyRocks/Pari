### 服务层
该层只负责逻辑处理, 用Zephire把所有的业务逻辑处理

例如:
zephire 生成一个casino.so的插件, 里面命名空间有一个QueryName的属性

```php
<?php
   class testController extends ControllerBase{

       public function IndexAction(){
             $name = $this->response->getQuery('name');
             //以下为Zephire所写插件的实例
             $temp = \Pari\QueryName::getName($name); //假设这个为检查用户名
             //方法1: 以下为调用数据库,Phalcon自带,数据库查询非常快
             $robot = new Robots();
             $robot->name = "WALL·E";
             $robot->save();
             //方法2: 使用静态方法直接读取
             $robot = Robots::name;
             $robot->save();

       }
   }
?>
```
> Model
```php
    class Robots extends \Phalcon\Mvc\Model
    {
        static public $name;
    }

```