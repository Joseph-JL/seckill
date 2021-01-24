<?php
session_start();

//当用户未通过登录界面进入此页面时 返回登录界面并弹出警告
if(!(isset($_SESSION['isLogin'])&&$_SESSION['isLogin']==1)){
    
  exit('<script>alert("重新登录");location.href="login.php";</script>');
}

?>


<!DOCTYE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>登陆成功，首页!</title>
    </head>
    
    <body>
        <h1>登陆成功，首页</h1>
        <p><?php echo "欢迎您：",$_COOKIE['username']; ?> </p>
    </body>
</html>