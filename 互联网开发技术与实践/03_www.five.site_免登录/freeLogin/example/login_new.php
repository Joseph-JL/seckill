    <?php
    if($_POST['sub']){  //用户点击提交 获取用户输入数据
      $username = $_POST['username'];
      $password = $_POST['password'];
      $checked = $_POST['chk'];
      if($username == 'root' && $password == 123){ //用户名与密码都正确
          if(!empty($checked)){   //勾选了7天免登录
              setcookie('username', $username, time()+604800);
          }else{  //拒绝7天免登录
              setcookie('username', $username);
          }
          setcookie('isCheck', $checked);
          setcookie('isLogin', 1);
          header('Location:index.php');
      }else{  //用户名或密码错误
              echo "<script>alert('用户名或密码错误！');</script>";
      }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>用户登录</title>
      <style>
          *{
              padding: 0;
              margin: 0;
              box-sizing: border-box;
          }
          .login{
              margin: 60px auto;
              max-width: 400px;
              height: 250px;
              border: 5px solid green;
              display: flex;
              flex-flow: column nowrap;
          }
          .login > h1{
              font-size: 20px;
              margin: 10px;
              text-align: center;
          }
          .login > form{
              display: flex;
              flex-flow: column nowrap;
              align-items: center;
          }
          .login > form > *{
              width: 280px;
              height: 40px;
              margin: 10px;
          }
          .login > form > div{
              display: flex;
              justify-content: space-between;
          }
          .login > form > div > input{
              width: 100px;
              background-color: white;
              border: 1px solid green;
          }
          .login > form > div > div{
              font-size: 12px;
              line-height: 40px;
          }
      </style>
    </head>
    <body>
      <div class="login">
          <h1>用户登录</h1>
          <form action="" method="post">
              <input type="text" name='username' placeholder="用户名" require>
              <input type="password" name='password' placeholder="密码" require>
              <div>
                  <div><input type="checkbox" name='chk' checked>7天自动登录</div>
                  <input type="submit" name='sub' value='提交'>
              </div>
          </form>
      </div>
    </body>
    </html>