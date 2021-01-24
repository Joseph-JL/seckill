<?php
namespace app\admin\controller;

use think\Controller;
use think\Loader;

class Login extends Base
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
            $info=db('crm_user')->field('id,username,password')->where('username',$username)->find();
            if(!$info){
                $this->error('用户不存在');
            }
            
            if(md5($password)!=$info['password']){
                $this->error('密码不正确');
            }
            else{
                session('username',$info['username']);
                session('userid',$info['id']);
                //记录登录信息
                //Loader::model('Admin')->editInfo(1,$info['id']);
                $this->success('登录成功','Index/index');
            }
            
            
        }
        
        
        return $this->fetch('login');
        
    }
}
?>