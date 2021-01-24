<?php
session_start();

//用户点击了提交，获取用户数据.
//先判断，若是新登录，先执行后面的html，无需两个页面，好的处理方式。
if($_POST['sub'])
{
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $checked=$_POST['checked'];//7天免登录是否勾选



    //连接数据库
    header("Content-type:text/html;charset=utf-8");
    $link = mysqli_connect('localhost','sql_101_200_78_1','liu123456','test');
    if (!$link) {
     die("连接失败:".mysqli_connect_error());
    }
    
    $sql="select username,password,id from crm_user where username = '$username'";
    /*
    $sql="SELECT username,password,   crm_use_role.role_id 
    FROM crm_user 
    INNER JOIN crm_user_role 
    ON crm_user.id=crm_user_role.user_id
    
    WHERE crm_user.username = '$username'
    ";
    */
    $result=mysqli_query($link,$sql);
    $row=mysqli_fetch_array($result);
    
    
    if(!$row){
    //判断用户是否存在
    //不安全 echo "<script>alert('用户未注册，请先注册！');window.location.href='zhuce.html'</script>";
     echo "<script>alert('用户名或密码错误，请重新登陆!');</script>";
    } 
    
    
    
    if($row["password"]==$password)//用户存在，且密码正确
    {
        if(!empty($checked))
        {
            //（方法一：利用$_SESSION["username"]判断免登录）
            // $_SESSION["username"]=$username;
            
            //one★★★重置cookie中sessionid的有效时间,否则关闭浏览器cookie中sessionid便被清除。下次再登录sessionid重新创建
            //(同时也要设置浏览器session的有效时间)
            $lifeTime=7*24*60*60;
            setcookie(session_name(),session_id(),time()+$lifeTime,"/");
            
            
            //two方法二：设置token，用于判断免登录
            $key="secretkey";//token加密秘钥
            $token=md5(session_id . date('Y-m-d',time()) . $key);
            setcookie('token',$token,time()+$lifeTime,"/");
            
            
        }
        
        //three不论是本次一次登录，还是免登录，本次设置$_SESSION["isLogin"]，判断状态
        $_SESSION['isLogin']=1;//也只是在欢迎页welcome.php中用于判断是否登录
        $_SESSION['isCheck']=1;//只是为了判断状态，$_SESSION["isCheck"]在此处并没有用到
        
        $_SESSION['username']=$username;//即使是一次登录也需要用到username
        
        
        
        //four多表查询role_id,设置，$_SESSION['role_id']★★★
        $sql1="SELECT  crm_user.username,crm_user_role.role_id 
        FROM crm_user 
        
        INNER JOIN crm_user_role 
        ON crm_user.id=crm_user_role.user_id
    
        WHERE crm_user.username = '$username'
        ";
        $result1=mysqli_query($link,$sql1);
        $row1=mysqli_fetch_array($result1);
        
        $role_id=$row1["role_id"];
        $_SESSION['role_id']=$role_id;//本次设置$_SESSION['role_id'];
        
        
        
        
        //登陆成功，进入欢迎页
        header("Location:welcome.php");
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
  <title>用户登录页面</title>
  <meta charset="UTF-8"/>
</head>

<body>
        <h1>用户登录</h1>
        
        <form action="" method="post">
            用户名:<input type="text" name="username" placeholder="用户名" require>
            <br>
            密码：<input type="password" name="password" placeholder="密码" require>
            <div><input type="checkbox" name="checked">7天免登录</div>
            <input type="submit" name="sub" value="提交">

        
    
        <br><br>&nbsp;
        没有账号请点这里<a href="zhuce.html">注册</a>!
    


</form>

</body>

</html>
