<?php
session_start();

header("Content-type:text/html;charset=utf-8");

$link = mysqli_connect('localhost','sql_101_200_78_1','liu123456','test');

if (!$link) {
 die("连接失败:".mysqli_connect_error());
}

$username = $_POST['username'];
$password = md5($_POST['password']);
$confirm = md5($_POST['confirm']);
$email = $_POST['email'];


//输入数据验证
if($username == "" || $password == "" || $confirm == "" || $email == ""){
 echo "<script>alert('信息不能为空！重新填写');window.location.href='zhuce.html'</script>";
 //数据验证，信息填写不能为空
} 
elseif ((strlen($username) < 3)||(!preg_match('/^\w+$/i', $username))) {
 echo "<script>alert('用户名至少3位且不含非法字符！重新填写');window.location.href='zhuce.html'</script>";
 //判断用户名长度
}
elseif(strlen($password) < 5){
 echo "<script>alert('密码至少5位！重新填写');window.location.href='zhuce.html'</script>";
 //判断密码长度
}
elseif($password != $confirm) {
 echo "<script>alert('两次密码不相同！重新填写');window.location.href='zhuce.html'</script>";
 //检测两次输入密码是否相同
} 
elseif (!preg_match('/^[\w\.]+@\w+\.\w+$/i', $email)) {
 echo "<script>alert('邮箱不合法！重新填写');window.location.href='zhuce.html'</script>";
 //判断邮箱格式是否合法
} 
elseif(mysqli_fetch_array(mysqli_query($link,"select * from crm_user where username = '$username'"))){
 echo "<script>alert('用户名已存在');window.location.href='zhuce.html'</script>";
 //用户名已存在，重新注册
} 



//验证成功，插入数据库
else{
 $sql= "insert into crm_user(username, password, email)values('$username','$password','$email')";
 //插入数据库
 if(!(mysqli_query($link,$sql))){
   echo "<script>alert('数据插入失败,请重新注册');window.location.href='zhuce.html'</script>";
 }
 else{
   echo "<script>alert('注册成功，请确定登录');window.location.href='login.php'</script>";
 }
}

?>