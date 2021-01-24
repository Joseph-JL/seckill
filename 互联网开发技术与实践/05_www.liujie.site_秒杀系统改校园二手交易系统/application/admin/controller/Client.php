<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 顾客账号设置
 */
class Client extends Common
{
    /**
     * index/info 查看顾客账号
     */
    public function index()
    {
        $lists = db('client_user')->field('id,username,password,email')->paginate(25);
        $page = $lists->render();//页数显示
        
        $this->assign('lists', $lists); 
        $this->assign('page', $page); 
        
        $count= db('client_user')->count();//查询记录总数
        $this->assign('count', $count);
        
        return $this->fetch();
    }
    
    /**
     * add 添加顾客账号
     */
    public function add()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();

            $count = db('client_user')->where('username', $data['username'])->find();
            if ($count) {
                    $this->error('用户名已存在');
                }
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
            $md5_salt = config('md5_salt');
            $data['password'] = md5(md5($data['password']).$md5_salt);
            
            //更新client_user表
            $res=db('client_user')->insert(['username' => $data['username'], 'password' => $data['password'],  'email' => $data['email']]);
            if($res){
                return $this->success('添加顾客账号成功','Client/index');
            }else{
                return $this->error('添加顾客账号失败！','Client/index');
            }
        }
        return $this->fetch();
    }
    
    /**
     * edit 编辑顾客账号、可管理权限
     * ▲id不可修改，因为使用id作为索引
     */
    public function edit()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            
            //▲id不可修改，需用Javascript限制输入
            
            //有修改才更新
            $user=db('client_user')->where('id', $data['id'])->field('id,username,password,email')->find();
            if($user['id']==$data['id']&&$user['username']==$data['username']&&$user['password']==$data['password']&&$user['email']==$data['email']){
                $this->error('提交未作修改','Client/index');
            }
            
            //修改的name不能与除去自身的已有name重复
            $user1= db('client_user')->where(array('id' => array('NEQ', $data['id'])))->select();
            foreach($user1 as $key=>$val){
                if($val['username']==$data['username']){
                    $this->error('该用户名已存在','Client/index');
                }
            }
            
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
            $md5_salt = config('md5_salt');
            $data['password'] = md5(md5($data['password']).$md5_salt);
            
            //更新client_user表
            $res=db('client_user')->where('id',$data['id'])->update(['username' => $data['username'],  'password' => $data['password'],'email' => $data['email']]);
            if($res){
                return $this->success('更新顾客账号成功！','Client/index');
            }else{
                return $this->error('更新顾客账号失败！','Client/index');
            }
        }
        
        $data1=input();
        $lists = db('client_user')->where('id', $data1['id'])->field('id,username,password,email')->find();
        $this->assign('lists', $lists); 
        return $this->fetch();
    }
    
    /**
     * del 删除顾客账号
     * 如果该顾客拥有商品，不能删除
     */
    public function del()
    {   
        $id=input('id');
        //如果该顾客拥有了商品
        $info=db('client_user')->where('id', $id)->find();
        $res1=db('goods_detail')->where('clientName', $info['username'])->find();
        
        if($res1){
            return $this->error('该顾客持有商品,不能删除!','Client/index');
        }else{//不持有商品的顾客，支持删除
            $res2=db('client_user')->where('id', $id)->delete();
            if($res2){
                return $this->success('顾客账号删除成功!','Client/index');
            }else{
                return $this->error('顾客账号删除失败!','Client/index');
            }
        }

    }
}