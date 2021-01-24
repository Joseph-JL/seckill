<?php


//判断是否登录，后面再判断登录后是否有权限
session_start();
//当用户未通过登录界面进入此页面时 返回登录界面并弹出警告
if(!(isset($_SESSION['isLogin'])&&$_SESSION['isLogin']==1)){
  exit('<script>
  alert("请重新登录！");
  location.href="http://101.200.78.125:805/freeLogin/crm_limit/login.php";
  </script>');
}


//one获取当前页面的url
function GetCurUrl() {
    $url = 'http://';
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $url = 'https://';
    }
     
    // 判断端口
    if($_SERVER['SERVER_PORT'] != '80') {
        $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .  $_SERVER['REQUEST_URI'];
    } else {
        $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['REQUEST_URI'];
    }
    
    return $url;
}

$url=GetCurUrl();




/*two 此时已知role_id(存在$_SESSION['role_id']里面)
通过role_id在表crm_access查询出对应的多个node_id
连接查询表crm_node中的name（url）(多个)
通过crm_access中的node_id=crm_node中的id

将查询结果存到一个数组中，多个url

再将本页面url和查询到的用户允许的url数组比较，看是否允许访问
*/
session_start();
$role_id=$_SESSION['role_id'];


$link = mysqli_connect('localhost','sql_101_200_78_1','liu123456','test');
    if (!$link) {
     die("连接失败:".mysqli_connect_error());
    }

$sql="SELECT  crm_node.name 
        FROM crm_access 
        
        INNER JOIN crm_node 
        ON crm_access.node_id=crm_node.id
    
        WHERE crm_access.role_id = '$role_id'
        ";
        $result=mysqli_query($link,$sql);//得到的是返回的二维表，有多行，但此处只去了一列
        
        if(mysqli_num_rows($result)>0)//查询结果不为0
        {
            while($row=mysqli_fetch_assoc($result)){
                
                if($row["name"]==$url)//比对成功，有权访问
                {
                    break;
                }
            }
            if(!$row)//比对失败，无权访问。此时$row=false
            {
                exit('<script>
                alert("无权访问，请登录授权访问!");
                location.href="http://101.200.78.125:805/freeLogin/crm_limit/login.php";
                </script>');
            }
            
        }
        

?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"> 
        <title>studentPage3</title>
    </head>
    
    <body>
        <h1>欢迎来到studentPage3页面！</h1>
    </body>
    
</html>