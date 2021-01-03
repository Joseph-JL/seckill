<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 管理分组角色
 */
class Group extends Common
{
    /**
     * index/info 查看分组
     */
    public function index()
    {
        $lists = db('admin_group')->field('id,name,description,rules')->select();
        $this->assign('lists', $lists);   
        return $this->fetch();
    }
    
    /**
     * add 添加分组
     */
    public function add()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();

            $count = db('admin_group')->where('name', $data['name'])->find();
            if ($count) {
                    $this->error('该角色已存在');
            }
            
            //更新admin_group表
            $res=db('admin_group')->insert(['name' => $data['name'], 'description' => $data['description'],'rules' => $data['rules']]);
            if($res){
                return $this->success('添加角色成功！','Group/index');
            }else{
                return $this->error('添加角色失败！','Group/index');
            }
        }
        return $this->fetch();
    }
    
    /**
     * edit 编辑分组角色
     * 只能编辑修改name、description
     */
    public function edit()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            
            //▲id不可修改，需用Javascript限制输入
            
            //有修改才更新
            $group=db('admin_group')->where('id', $data['id'])->field('id,name,description,rules')->find();
            if($group['id']==$data['id']&&$group['name']==$data['name']&&$group['description']==$data['description']&&$group['rules']==$data['rules']){
                $this->error('提交未作修改','Group/index');
            }
            
            //修改的name不能与除去自身的已有name重复
            $group1= db('admin_group')->where(array('id' => array('NEQ', $group['id'])))->select();
            foreach($group1 as $key=>$val){
                if($val['name']==$data['name']){
                    $this->error('该角色名已存在','Group/index');
                }
            }
            
            //更新admin_group表
            $res=db('admin_group')->where('id',$data['id'])->update(['name' => $data['name'],  'description' => $data['description'],'rules' => $data['rules']]);
            if($res){
                return $this->success('更新管理员账号成功！','Group/index');
            }else{
                return $this->error('更新管理员账号失败！','Group/index');
            }
        }
        
        $data1=input();
        $lists = db('admin_group')->where('id', $data1['id'])->field('id,name,description,rules')->find();
        $this->assign('lists', $lists); 
        return $this->fetch();
    }
    
    /**
     * del 删除角色
     * 如果存在分配了用户的角色，不支持删除；
     * 没有分配用户的角色，支持删除
     */
    public function del()
    {   
        $id=input('id');
        //如果存在分配了用户的角色，不支持删除；
        $res1=db('admin_group_access')->where('group_id', $id)->find();
        if($res1){
            return $this->error('该角色存在用户,不能删除!','Group/index');
        }else{//没有分配用户的角色，支持删除
            $res2=db('admin_group')->where('id', $id)->delete();
            if($res2){
                return $this->success('角色删除成功!','Group/index');
            }else{
                return $this->error('角色删除失败!','Group/index');
            }
        }
    }
}