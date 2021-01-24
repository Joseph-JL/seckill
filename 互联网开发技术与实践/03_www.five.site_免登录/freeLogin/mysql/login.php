<?php

session_start();

header("Content-type:text/html;charset=utf-8");
$link = mysqli_connect('localhost','sql_101_200_78_1','liu123456','test');

/*
if ($link) {
    
 echo "数据库连接成功";

}
*/
if (!$link) {

 die("连接失败:".mysqli_connect_error());

}

$username = $_POST['username'];
$password = md5($_POST['password']);

if($username == "" || $password == "")
{
 echo "<script>alert('信息不能为空！重新填写');window.location.href='zhuce.html'</script>";
}

elseif ((strlen($username) < 3)||(!preg_match('/^\w+$/i', $username))) 
{
 echo "<script>alert('用户名至少3位且不含非法字符！重新填写');window.location.href='zhuce'</script>";
 //判断用户名长度
}elseif(strlen($password) < 5){
 echo "<script>alert('密码至少5位！重新填写');window.location.href='zhuce.html'</script>";
 //判断密码长度
}

$sql="select username,password from login where username = '$username'";
$result=mysqli_query($link,$sql);
$row=mysqli_fetch_array($result);

if(!$row){
//判断用户是否存在
 echo "<script>alert('用户未注册，请先注册！');window.location.href='zhuce.html'</script>";
} 

if($row["password"]==$password)
{
        echo $username . ",恭喜您,登陆成功!";
    //跳转页面并传递参数
        header('Location:welcome.php?username='.$username);
    //确保重定向后，后续代码不会被执行
    exit;
}
else
{
    echo $row["password"] . "   " .$password;
    
    echo "<script>alert('密码错误，请重新登录！');window.location.href='login.html'</script>";
}

?>