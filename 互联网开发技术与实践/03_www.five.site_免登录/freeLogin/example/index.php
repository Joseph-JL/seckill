    <?php
    //当用户未通过登录界面进入此页面时 返回登录界面并弹出警告
    if(!(isset($_COOKIE['isLogin']) && $_COOKIE['isLogin'] == 1)){
      exit('
      <script>
          alert("重新登录");
          location.href="login.php";
      </script>
      ');
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>首页</title>
      <style>
          body{
              display: flex;
              flex-flow: column nowrap;
              text-align: center;
          }
          h1{
              margin: 0 auto;
          }
      </style>
    </head>
    <body>
      <h1>首页</h1>
      <p><?php echo '欢迎您:',$_COOKIE['username']; ?></p>
      <a href="login.php?action=quit">退出登录</a>
    </body>
    </html>