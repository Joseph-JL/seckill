<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 管理账号设置
 */
class Admin extends Common
{
    /**
     * index/info 查看管理员账号
     */
    public function index()
    {
        $lists = db('admin_user')->field('id,username,email,lastloginip,lastlogintime')->select();
        foreach($lists as $key=>$val){//查询管理员用户角色
            $group_access=db('admin_group_access')->where('uid', $val['id'])->find();
            $group=db('admin_group')->where('id', $group_access['group_id'])->find();
            $lists[$key]['groupName']=$group['name'];
        }
        $this->assign('lists', $lists);   
        return $this->fetch();
    }
    
    /**
     * add 添加管理员账号
     */
    public function add()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            //var_dump($data);
            $count = db('admin_user')->where('username', $data['username'])->find();
            if ($count) {
                    $this->error('用户名已存在');
                }
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
            $md5_salt = config('md5_salt');
            $data['password'] = md5(md5($data['password']).$md5_salt);
            
            //更新admin_user表
            db('admin_user')->insert(['username' => $data['username'], 'password' => $data['password'],  'email' => $data['email']]);
            //更新admin_group_access表
            $user = db('admin_user')->where('username', $data['username'])->find();
            //var_dump($user);
            db('admin_group_access')->insert(['uid' => $user['id'], 'group_id' => $data['group_id']]);
            
            
            $lists = db('admin_user')->field('id,username,email,lastloginip,lastlogintime')->select();
            $this->assign('lists', $lists);   
            return $this->success('成功添加管理员账号！','Admin/index');
        }
        
        $lists = db('admin_group')->field('id,name')->select();
        $this->assign('lists', $lists); 
        return $this->fetch();
    }
    
    /**
     * edit 编辑管理员账号、可管理权限
     * ▲用户名不可修改，因为使用用户名作为索引
     */
    public function edit()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            
            //有修改才更新
            $user1=db('admin_user')->where('username', $data['username'])->field('id,username,password,email')->find();
            $usergroup1=db('admin_group_access')->where('uid', $user1['id'])->field('group_id')->find();
            if($user1['username']==$data['username']&&$user1['password']==$data['password']&&$user1['email']==$data['email']&&$usergroup1['group_id']==$data['group_id']){
                $this->error('提交未作修改','Admin/index');
            }
            
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
            
            //修改的用户名不能与除去自身的已有用户名重复
            $users2 = db('admin_user')->where(array('username' => array('NEQ', $data['username'])))->select();
            foreach($users2 as $key=>$val){
                if($val['username']==$data['username']){
                    $this->error('该用户名已存在','Admin/index');
                }
            }
            //▲用户名不可修改，因为使用用户名作为索引
            
            //更新admin_user表
            db('admin_user')->where('username',$data['username'])->update(['email' => $data['email']]);
            //密码变了才能更新，否则原密码再次彩虹加密，出错
            if($data['password']!=$user1['password']){
                $md5_salt = config('md5_salt');
                $data['password'] = md5(md5($data['password']).$md5_salt);
                db('admin_user')->where('username',$data['username'])->update(['password' => $data['password']]);
            }
            
            //更新admin_group_access表
            $user = db('admin_user')->where('username', $data['username'])->find();
            db('admin_group_access')->where('uid', $user['id'])->update(['group_id' => $data['group_id']]);
            
            
            $lists = db('admin_user')->field('id,username,email,lastloginip,lastlogintime')->select();
            $this->assign('lists', $lists);   
            return $this->success('成功更新管理员账号！','Admin/index');
        }
        
        
        $data1=input();
        $lists = db('admin_user')->where('username', $data1['username'])->field('username,password,email')->find();
        $this->assign('lists', $lists); 
        
        //将用户group_id放到开头 
        //▲编辑时用户与角色的匹配做
        $user = db('admin_user')->where('username', $data1['username'])->find();
        $usergroup=db('admin_group_access')->field('group_id')->where('uid', $user['id'])->find();
        
        $lists2 = db('admin_group')->field('id,name')->select();
        $tmp=$lists2[0];
        foreach($lists2 as $key=>$val){
            if($val['id']==$usergroup['group_id']){
                $lists2[0]=$lists2[$key];
                $lists2[$key]=$tmp;
                break;
            }
        }
        $this->assign('lists2', $lists2); 

        return $this->fetch();
    }
    
    /**
     * del 删除管理员账号
     */
    public function del()
    {   $username=input('username');

        $user = db('admin_user')->where('username',$username)->find();
        //更新admin_user表
        $res1=db('admin_user')->where('username', $username)->delete();
        //更新admin_group_access表
        $res2=db('admin_group_access')->where('uid', $user['id'])->delete();
            
        if($res1&&$res2){
            return $this->success('删除管理员账号成功!','Admin/index');
        }else{
            return $this->error('删除管理员账号失败!','Admin/index');
        }

    }


    /**
     * public_edit_info 个人设置/修改密码     * 
     * ▲id不可修改，因为使用用户名作为索引
     * 用户名可以修改
     */
    public function public_edit_info()
    {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $user_id=session('user_id');
            $data = input();
            
            //有修改才更新
            $user1=db('admin_user')->where('id', $user_id)->field('id,username,password,email')->find();
            $usergroup1=db('admin_group_access')->where('uid', $user_id)->field('group_id')->find();
            if($user1['username']==$data['username']&&$user1['password']==$data['password']&&$user1['email']==$data['email']&&$usergroup1['group_id']==$data['group_id']){
                $this->error('提交未作修改','Admin/public_edit_info');
            }
            
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致','Admin/public_edit_info');
            }
            
            //修改的用户名不能与除去自身的已有用户名重复
            $users2 = db('admin_user')->where(array('id' => array('NEQ', $user_id)))->select();
            foreach($users2 as $key=>$val){
                if($val['username']==$data['username']){
                    $this->error('该用户名已存在','Admin/public_edit_info');
                }
            }
            //▲用户名可以修改,用user_id作为索引
            
            //更新admin_user表
            db('admin_user')->where('id',$user_id)->update(['username' => $data['username'], 'email' => $data['email']]);
            //密码变了才能更新，否则原密码再次彩虹加密，出错
            if($data['password']!=$user1['password']){
                $md5_salt = config('md5_salt');
                $data['password'] = md5(md5($data['password']).$md5_salt);
                db('admin_user')->where('id',$user_id)->update(['password' => $data['password']]);
            }
            
            //更新admin_group_access表
            db('admin_group_access')->where('uid', $user_id)->update(['group_id' => $data['group_id']]);
            
            $lists = db('admin_user')->field('id,username,email,lastloginip,lastlogintime')->select();
            $this->assign('lists', $lists);   
            return $this->success('成功更新个人设置信息和密码,请重新登陆！','Login/logout');
        }
        
        
        $user_id=session('user_id');
        $lists = db('admin_user')->where('id', $user_id)->field('username,password,email')->find();
        $this->assign('lists', $lists); 
        
        //将用户group_id放到开头 
        //▲编辑时用户与角色的匹配做
        $usergroup=db('admin_group_access')->field('group_id')->where('uid', $user_id)->find();
        
        $lists2 = db('admin_group')->field('id,name')->select();
        $tmp=$lists2[0];
        foreach($lists2 as $key=>$val){
            if($val['id']==$usergroup['group_id']){
                $lists2[0]=$lists2[$key];
                $lists2[$key]=$tmp;
                break;
            }
        }
        $this->assign('lists2', $lists2); 

        return $this->fetch();
    }
    
}