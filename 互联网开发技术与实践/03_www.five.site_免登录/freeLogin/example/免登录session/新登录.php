<?php

//用户点击了提交，获取用户数据.
//先判断，若是新登录，先执行后面的html，无需两个页面，好的处理方式。
if($_POST['sub'])
{
    $username=$_POST['username'];
    $password=$_POST['password'];
    $checked=$_POST['checked'];//7天免登录是否勾选
    
    if($username=='liujie'&&$password==123456)//用户名和密码正确
    {
        if(!empty($checked))
        {
            setcookie('username',$username,time()+60*60*24*7);//勾选了7天免登录，设置cookie值，下次免登录
        }
        else//没有勾选7天免登录，不设置cookie值，设置本次cookie，有效期至浏览器关闭。下次打开浏览器还要登录。
        {
            setcookie('username',$username);
        }
        
        
        setcookie('isCheck',$checked);//设置本次的选择
        setcookie('isLogin',1);
        
        //登陆成功，进入欢迎页
        header('Location:欢迎.php');
    }
    
    else//用户名和密码错误
    {
        echo "<script>alert('用户名或密码错误，请重新登陆!');</script>";
    }
    
}
?>


<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <title>用户登录</title>
    </head>
    
    <body>
        <h1>用户登录</h1>
        
        <form action="" method="post">
            用户名:<input type="text" name="username" placeholder="用户名" require>
            <br>
            密码：<input type="password" name="password" placeholder="密码" require>
            <div><input type="checkbox" name="checked">7天免登录</div>
            <input type="submit" name="sub" value="提交">
        </form>
        
    </body>

</html>