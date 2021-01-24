<?php
session_start();

//服务器计算token，也可直接将token存入$_SESSION[]中，但是服务器中$_SESSION[]会很大
$key="secretkey";//token加密秘钥
$token=md5(session_id . date('Y-m-d',time()) . $key);

//判断本地是否用户登陆过的cookie(本地是否有用户登陆过)
if($_COOKIE["token"]==$token)
//或者使用isset($_SESSION["username"])
{//本地用户登陆过，直接免登录进入欢迎页
    //setcookie('username',$_COOKIE['username'],time()+60*60*24*7);//重置7天时间
    //setcookie('isLogin',1);
    //setcookie('isCheck',1);
    
    /*
    $_SESSION["isLogin"]=1;
    $_SESSION["isCheck"]=1;
    */
    //用了$_SESSION以后，这里的重置没有意义，session变量一直在服务器中存储着
    //如果cookie中sessionid失效，根本就找不到上次的sessionid文件，会当成新用户，重新申请新的sessionid。并且找不到的session就成了"垃圾",后期会被系统回收
    
    header('location:welcome.php');
}
else
{//本地没有用户登陆过的cookie，进入新登录页面
    //setcookie('token','',0);//删除cookie，失效
    header('Location:login.php');
}

?>
