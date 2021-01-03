<?php
namespace app\client\controller;

use think\Controller;
use think\Loader;

class Login extends Controller
{
    public function index()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            
            $username = input('post.username');
            $password = input('post.password');

            if (!$username) {
                $this->error('用户名不能为空');
            }
            if (!$password) {
                $this->error('密码不能为空');
            }
            
            //查询数据库，验证用户名和密码
            $info=db('client_user')->field('id,username,password')->where('username',$username)->find();
            if(!$info){
                $this->error('用户不存在');
            }
            
            // if(md5($password)!=$info['password']){
            //     $this->error('密码不正确');
            // }
            if($password!=$info['password']){
                $this->error('密码不正确');
            }
            
            // //验证输入验证码正确
            // $inputcode=input("checkcode");//输入的验证码
            // if(!captcha_check($inputcode)){//图片真实验证码
            //     $this->error('验证码错误,请重新登陆！','Login/index');
            // }
            
            
            else{
                session('username',$info['username']);
                session('userid',$info['id']);
                //记录登录信息
                //Loader::model('Admin')->editInfo(1,$info['id']);
                $this->success('登录成功','Index/index');
            }
            
            
        }
        
        
        return $this->fetch();
        
    }
}
?>