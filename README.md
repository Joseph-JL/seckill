# seckill部署说明
## 一、新建ThinkPHP站点+数据库生成

1、通过宝塔一键部署thinkphp；

2、通过SQL语句生成数据库；

## 二、ThinkPHP部署

1、通过宝塔一键部署thinkphp；（如已部署请忽略）

2、导入静态资源资源

将“seckill\src\TP5_Client_Admin\public\static”里面的“xadmin"新建站点的相应位置。

3、修改application的配置文件config.php

1）添加'md5_salt'、修改调试模式

```php
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    //MD5
    'md5_salt'               => 'seckill',

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => true,
```



2）在config.php最后加上“设置模板”

```php
    //设置模板
    'template'  =>  [
    'layout_on'     =>  true,
    'layout_name'   =>  'layout',
    ],
```

4、连接数据库配置，修改database.php

```php
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => 'localhost',
    // 数据库名
    'database'        => 'seckill',
    // 用户名
    'username'        => 'seckill',
    // 密码
    'password'        => 'liu123456',
```

5、在宝塔中修改数据库访问权限为“所有人”或”指定IP“，本次设计采用了”所有人“；

###### ![image-20210103130723176](image/image-20210103130723176.png)

6、在宝塔中“安全”放行系统中所用到的端口，为了方便，此处放行了所有端口；

![image-20210103130911255](image/image-20210103130911255.png)

7、将TP5_Client_Admin\application里面的“admin”和“client”文件夹复制到新建站点的application文件夹下

client对应系统用户端程序

admin对应系统管理端程序

8、修改秒杀请求地址

修改“client\controller\Seckill.php\doseckill()"第85行$url,值修改为运行orderProduce1.go的地址，order此时应该运行在服务器上

```php
$url="http://101.200.78.125:10000/api/room/order?username=".$username;
```

## 三、订单生成和订单处理程序部署

1、修改orderProduce1.go第65行，修改为RabbitMQ的地址，连接RabbitMQ;

```go
conn, err := amqp.Dial("amqp://admin:admin@101.200.78.125:5672/")
```

2、修改orderProcess1.go第77行，修改为RabbitMQ的地址，连接RabbitMQ;

```go
conn, err := amqp.Dial("amqp://admin:admin@101.200.78.125:5672/")
```

3、修改orderProcess1.go第134行，修改为数据库地址

```go
dsn := "seckill:liu123456@tcp(120.53.251.22:3306)/seckill?charset=utf8mb4&parseTime&loc=Local" //数据库名+数据库密码
```

4、将orderProduce1.go和orderProcess1.go分别交叉编译，放到服务器上去运行；

orderProduce1.go必须放到服务器上运行；

orderProcess1.go可以放到服务器上去运行，也可以放在本地运行；

在linux服务器上运行orderProduce1和orderProcess1；



5、检查“二”中第8步

“client\controller\Seckill.php\doseckill()"第85行$url,值修改为运行orderProduce1.go的地址

6、注意运行orderProduce1.go的服务器如果安装了宝塔，一定要在宝塔”安全“里面放行相应端口，此处放心了所有端口，否则数据库，RabbitMQ，还有各种请求很可能被拦截，运行不成功。



## 部署完成！













