<?php
namespace app\admin\controller;
use think\Loader;

use think\Controller;

class Useradmin extends Base
{
    //管理员管理**********************************************************************************************************************************
    public function index()
    {
        session_start();
        //连接数据库取值
        //db('crm_user')->insert(['username' => 'liujie', 'password' => 'liujie', 'email' => 'liujie@qq.com',  'role_id' => '1', 'role_name' => '学生']);
        
        $lists = db('crm_user')->alias('t1')
                //->where(session('username'))
                //->join(config('database.prefix').'crm_user_role t2', 't1.id=t2.user_id', 'left')
                ->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
                ->group('t1.id')
                ->order('t1.id')
                ->select();
                
        
        //$info = db('crm_user')->select();
        //var_dump($lists);
        $this->assign('lists', $lists);
        return $this->fetch();
    }
    
    
     /*
     * 查看
     */

    public function info() {
        // $id = input('id');
        // if ($id) {
        //     //当前用户信息
        //     $info = model('Admin')->getInfo($id);
        //     $info['userGroups'] = Loader::model('Admin')->getUserGroups($id);
        //     $this->assign('info', $info);
        // }

        //所有组信息
        //$groups = model('AdminGroup')->getGroups();

        //$this->assign('groups', $groups);
        return $this->fetch();
    }
    
     /*
     * 添加
     */

    public function add() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        
        if($dosubmit) {
            
            $data = input();
            $count = db('crm_user')->where('username', $data['username'])->find();
    
            if ($count) {
                    $this->error('用户名已存在');
                }
    
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
    
            $data['password'] = md5($data['password']);
            //var_dump($data);
            //return $this->fetch('index');
            
            if (isset($data['student'])){
                $role_id=1;
                $role_name="学生";
            }elseif (isset($data['teacher'])){
                $role_id=2;
                $role_name="老师";
            }elseif (isset($data['admin'])){
                $role_id=3;
                $role_name="管理员";
                
            }
            //数据加入数据库
            //var_dump($data);
            db('crm_user')->insert(['username' => $data['username'], 'password' => $data['password'],  'email' => $data['email'], 'role_id' => $role_id]);
            
            
            
            //查询数据库渲染index
            $lists = db('crm_user')->alias('t1')
                //->where(session('username'))
                //->join(config('database.prefix').'crm_user_role t2', 't1.id=t2.user_id', 'left')
                ->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
                ->group('t1.id')
                ->order('t1.id')
                ->select();
            //$info = db('crm_user')->select();
            //var_dump($lists);
            $this->assign('lists', $lists);
            return $this->fetch('index');

        }
        
        return $this->fetch();
        
    }

    /*
     * 修改
     */

    public function edit() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            
            $data = input();
            $count = db('crm_user')->where('username', $data['username'])->find();
    
            // if ($count) {
            //         $this->error('用户名已存在');
            //     }
    
            if ($data['password'] != $data['confirm']) {
                $this->error('两次密码不一致');
            }
    
            $data['password'] = md5($data['password']);
            var_dump($data);
            //return $this->fetch('index');
            
            if (isset($data['student'])){
                $role_id=1;
                $role_name="学生";
            }elseif (isset($data['teacher'])){
                $role_id=2;
                $role_name="老师";
            }elseif (isset($data['admin'])){
                $role_id=3;
                $role_name="管理员";
                
            }
            //数据库用户数据更新
            //var_dump($data);
            //db('user')->where('id',1)->update(['name' => 'thinkphp']);
            db('crm_user')->where('username',$data['username'])->update([ 'password' => $data['password'],  'email' => $data['email'], 'role_id' => $role_id]);
            
            
            
            //查询数据库渲染index
            $lists = db('crm_user')->alias('t1')
                //->where(session('username'))
                //->join(config('database.prefix').'crm_user_role t2', 't1.id=t2.user_id', 'left')
                ->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
                ->group('t1.id')
                ->order('t1.id')
                ->select();
            //$info = db('crm_user')->select();
            //var_dump($lists);
            $this->assign('lists', $lists);
            return $this->fetch('index');
        }
        $data1 = input();//未提交编辑修改的数据
        
        //个人信息修改、密码修改
        if($data1['username']=="myself"){
            $data1['username']=session('username');
        }
        
        $lists = db('crm_user')->alias('t1')
        ->where('username', $data1['username'])
        ->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
        ->find();
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /*
     * 删除
     */

    public function del() {
        $username = input('username');
        $res = db('crm_user')->where(['username' => $username])->delete();
        
        if ($res) {
            //db('admin_group_access')->where(['uid' => $id])->delete();
            $this->success('删除用户成功', url('index'));
        } else {
            $this->error('删除用户失败', url('index'));
        }
    }
    
    
    //分组管理*************************************************************************************************************************************
    /*
    *当前分组
    */
    public function groupindex()
    {
        session_start();
        //连接数据库取值
        //db('crm_user')->insert(['username' => 'liujie', 'password' => 'liujie', 'email' => 'liujie@qq.com',  'role_id' => '1', 'role_name' => '学生']);
        
        $lists = db('crm_role')->alias('t1')
                //->where(session('username'))
                //->join(config('database.prefix').'crm_user_role t2', 't1.id=t2.user_id', 'left')
                //->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
                ->group('t1.id')
                ->order('t1.id')
                ->select();
                
        
        //$info = db('crm_user')->select();
        //var_dump($lists);
        $this->assign('lists', $lists);
        return $this->fetch();
    }
    
    
    
     /*
     * 查看
     */

    // public function info() {
    //     // $id = input('id');
    //     // if ($id) {
    //     //     //当前用户信息
    //     //     $info = model('Admin')->getInfo($id);
    //     //     $info['userGroups'] = Loader::model('Admin')->getUserGroups($id);
    //     //     $this->assign('info', $info);
    //     // }

    //     //所有组信息
    //     //$groups = model('AdminGroup')->getGroups();

    //     //$this->assign('groups', $groups);
    //     return $this->fetch();
    // }
    
     /*
     * 添加（添加分组）
     */

    public function groupadd() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        
        if($dosubmit) {
            $data = input();
            //组名已使用
            $count1 = db('crm_role')->where('name', $data['name'])->count();
            if ($count1) {
                $this->error('该组已存在，不能再次添加!');
            }
    
            //输入正确，新建分组数据加入数据库表crm_role
            $res=db('crm_role')->insert(['name' => $data['name'], 'statement' => $data['statement']]);
            
            if($res){
                $this->success('新建分组成功', url('/admin/Useradmin/groupindex'));
            }else{
                $this->error('新建分组失败!');
            }
        }
        return $this->fetch();
    }

    /*
     * 修改（修改分组）
     */

    public function groupedit() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        
        if($dosubmit) {
            $data = input();
            
            //分组名不能修改，但此处只能限制改为系统不存在的
            $info = db('crm_role')->where('name', $data['name'])->count();
            if(!$info){
                $this->error('分组名不能修改!');
            }
            
            //输入正确，数据库表crm_role数据更新，此处只能修改说明
            $res=db('crm_role')->where('name',$data['name'])->update([ 'statement' => $data['statement']]);
            if($res){
                $this->success('分组编辑修改成功', url('/admin/Useradmin/groupindex'));
            }else{
                $this->error('分组编辑修改失败!');
            }
        }
        
        
        $data1 = input();
        //未提交编辑修改的数据,原来的数据
        $lists = db('crm_role')->where(['name' => $data1['name']])->find();
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /*
     * 删除（删除分组）
     */

    public function groupdel() {
        //删除分组容易出现一个问题：分组删除了，但是原来分配给分组的成员的分组信息仍然存在，所以只能删除未分配组成员的分组
        $username = input('name');
        
        //表crm_role，删除分组
        $res = db('crm_role')->where('name', $username)->delete();
        
        if ($res) {
            $this->success('删除分组成功', url('/admin/Useradmin/groupindex'));
        } else {
            $this->error('删除分组失败', url('/admin/Useradmin/groupindex'));
        }
    }
    
    
    
    
    
    
    
    
}

?>