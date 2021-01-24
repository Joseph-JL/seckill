<?php

//判断本地是否用户登陆过的cookie(本地是否有用户登陆过)
if(isset($_COOKIE['username']))
{//本地用户登陆过，直接免登录进入欢迎页
    setcookie('usename',$_COOKIE['username'],time()+60*60*24*7);//重置7天时间
    setcookie('isLogin',1);
    setcookie('isCheck',1);
    header('location:欢迎.php');
}
else
{//本地没有用户登陆过的cookie，进入新登录页面
    setcookie('username','',0);//删除cookie，失效
    setcookie('isLogin','',0);
    setcookie('isCheck','',0);
    header('Location:新登录.php');
}

?>