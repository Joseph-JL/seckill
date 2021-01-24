<?php

session_start();

header("Content-type:text/html;charset=utf-8");

$link = mysqli_connect('localhost','sql_101_200_78_1','liu123456','test');
if (!$link) {

 die("连接失败:".mysqli_connect_error());

}

//计算程序运行时间
$t1=microtime(true); //获取程序1，开始的时间
for($i=12000;$i<=110000;$i++)
{
    $i2str=(string)$i;
    
    $username="zhangsan".$i2str;
    $password="liujie".$i2str;
    $confirm="liujie".$i2str;
    $email="email".$i2str;
    
    $sql= "insert into login(username, password, confirm, email)values('$username','$password','$confirm','$email')";

//插入数据库

 if(!(mysqli_query($link,$sql))){

   echo "插入数据失败，注册失败";
   exit;
   /*"<script>alert('数据插入失败');window.location.href='zhuce.html'</script>";*/

 }
 /*
 else{

   echo "恭喜您,注册成功!";
   
 }
 */
}
$t2=microtime(true); //获取程序1，结束时间
$time=$t2-$t1;
echo $time;
echo "\n完成!"

?>