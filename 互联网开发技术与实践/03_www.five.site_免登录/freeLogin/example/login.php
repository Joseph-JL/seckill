    <?php
    function direct_login(){
      setcookie('username', $_COOKIE['username'], time()+604800); //重置7天时间
      setcookie('isLogin', 1);
      setcookie('isCheck', 1);
      header('Location:index.php'); //直接进入主页 免登录
    }
    function new_account_login(){
      setcookie('username', '', 0);
      setcookie('isLogin', '', 0);
      setcookie('isCheck', '', 0);
      header('Location:login_new.php'); //跳转到登录页面
    }
    //判断本地是否有用户登录过的cookie
    if(isset($_COOKIE['username'])){
      //选择是否使用新账号登录
      if($_POST['old_account']){  //免密直接登录
          direct_login();
      }else if($_POST['new_account']){  //新账号登录
          new_account_login();
      }
    }else{  //本地没有用户登录过的cookie
      new_account_login();
    }
    //退出登录
    if($_GET["action"] == 'quit'){
      if(empty($_COOKIE['isCheck'])){ //没有选择7天免登录
          new_account_login();
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
          form{
              margin: 60px auto;
              max-width: 400px;
              height: 110px;
              border: 5px solid green;
              display: flex;
              flex-flow: column nowrap;
              align-items: center;
          }
          form > input{
              width: 280px;
              height: 40px;
              margin: 10px;
              background-color: white;
              border: 1px solid green;
          }
      </style>
    </head>
    
    
    <body>
      <form action="" method="post" >
          <input type='submit' name='old_account' value='使用<?php echo $_COOKIE['username'];?>账号登录'  />
          <input type='submit' name='new_account' value='使用新账号登录' />
      </form>
    </body>
    </html>