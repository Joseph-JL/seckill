<?php
session_start();
//当用户未通过登录界面进入此页面时 返回登录界面并弹出警告
if(!(isset($_SESSION['isLogin'])&&$_SESSION['isLogin']==1)){
  exit('<script>alert("请重新登录！");location.href="login.php";</script>');
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
        <p>
            
        <?php //原始单一欢迎界面
        session_start(); echo "欢迎您：",$_SESSION['username'];
        ?> 
        
        <br><br>您可以访问以下页面：<br>
        
        <?php //学生、老师、管理员欢迎界面
        session_start();
        
        //学生可以访问的页面
        if($_SESSION['role_id']==1)
        {
            echo '
            <html>
                <head>
                    <meta charset="utf-8">
                </head>
                <body>
        
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage1.php" target="_blank">studentPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage2.php" target="_blank">studentPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage3.php" target="_blank">studentPage3</a><br>

                 </body>
            </html>
            ';
        }
        
        //老师可以访问的页面
        else if($_SESSION['role_id']==2)
        {
            echo '
            <html>
                <head>
                    <meta charset="utf-8">
                </head>
                <body>
        
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage1.php" target="_blank">studentPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage2.php" target="_blank">studentPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage3.php" target="_blank">studentPage3</a><br>
                <br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage1.php" target="_blank">teacherPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage2.php" target="_blank">teacherPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage3.php" target="_blank">teacherPage3</a><br>

                 </body>
            </html>
            ';
        }
        
        //教务管理员可以访问的页面
        else if($_SESSION['role_id']==3)
        {
            echo '
            <html>
                <head>
                    <meta charset="utf-8">
                </head>
                <body>
        
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage1.php" target="_blank">studentPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage1.php" target="_blank">studentPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/studentPage1.php" target="_blank">studentPage3</a><br>
                <br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage1.php" target="_blank">teacherPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage2.php" target="_blank">teacherPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/teacherPage3.php" target="_blank">teacherPage3</a><br>
                <br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/adminPage1.php" target="_blank">adminPage1</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/adminPage2.php" target="_blank">adminPage2</a><br>
                <a href="http://101.200.78.125:805/freeLogin/crm_limit/diffRolePage/adminPage3.php" target="_blank">adminPage3</a><br>

                 </body>
            </html>
            ';
        }
        
        
        ?>
        
        </p>
        
        
        <?php //header('location:diffRolePage/studentPage1.php'); ?>
        
    </body>
</html>

