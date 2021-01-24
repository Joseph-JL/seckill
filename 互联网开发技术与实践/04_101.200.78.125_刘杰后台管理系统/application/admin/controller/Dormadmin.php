<?php
namespace app\admin\controller;
use think\Loader;

use think\Controller;

class Dormadmin extends Base
{
    /*
    *楼层总体情况
    */
    public function index()
    {
        session_start();
        //连接数据库取值
        //db('crm_user')->insert(['username' => 'liujie', 'password' => 'liujie', 'email' => 'liujie@qq.com',  'role_id' => '1', 'role_name' => '学生']);
        
        $lists = db('dorm_total')->alias('t1')
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
    *宿舍已分配具体情况
    */
    
    public function allocated()
    {
        session_start();
        //连接数据库取值
        //db('crm_user')->insert(['username' => 'liujie', 'password' => 'liujie', 'email' => 'liujie@qq.com',  'role_id' => '1', 'role_name' => '学生']);
        
        $lists = db('dorm_allocated')->alias('t1')
                //->where(session('username'))
                //->join(config('database.prefix').'crm_user_role t2', 't1.id=t2.user_id', 'left')
                //->join(config('database.prefix').'crm_role t2', 't1.role_id=t2.id', 'left')
                //->group('t1.id')
                //order('id desc,status')
                ->order('building,floor,room,bed')
                ->select();
                
        
        //$info = db('crm_user')->select();
        //var_dump($lists);
        $this->assign('lists', $lists);
        return $this->fetch();
    }
    
    
    //宿舍操作*****************************************************************************************************
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
     * 添加（分配宿舍）
     */

    public function dormadd() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        
        if($dosubmit) {
            
            $data = input();
            //用户已分配宿舍
            $count1 = db('dorm_allocated')->where('username', $data['username'])->count();
            if ($count1) {
                $this->error('该用户已分配宿舍，不能再次分配!');
            }
            //宿舍已分配，不能再分配   
            $count2 = db('dorm_allocated')->where(['building' => $data['building'],'floor' => $data['floor'],'room' => $data['room'],'bed' =>  $data['bed']])->count();
            if ($count2) {
                $this->error('该宿舍已被分配，不能再次分配!');
            }
            
            
            //输入宿舍有效
            $info = db('dorm_total')->where('building', $data['building'])->find();
            //楼不存在
            if(!$info){
                $this->error('输入不正确!');
            }
            //层号，房间号，床号存在
            if($data['floor']<0||$data['floor']>$info['floor_num'] || $data['room']<0||$data['room']>$info['room_num']  ||  $data['bed']<0||$data['bed']>$info['bed_num']){
                $this->error('输入不正确!');
            }
                
                
    
            //输入正确，数据加入数据库
            //var_dump($data);
            //表dorm_allocated，添加用户
            db('dorm_allocated')->insert(['building' => $data['building'], 'floor' => $data['floor'],  'room' => $data['room'], 'bed' => $data['bed'], 'username' => $data['username']]);
            //表dorm_total，数据更新
            db('dorm_total')->where('building',  $data['building'])->setInc('allocated');
            db('dorm_total')->where('building',  $data['building'])->setDec('remained');
            
            
            
            $this->success('分配宿舍成功', url('/admin/Dormadmin/allocated'));
        }
        
        return $this->fetch();
        
    }

    /*
     * 修改（调换宿舍）
     */

    public function dormedit() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            
            $data = input();
            
            //输入宿舍有效
            $info = db('dorm_total')->where('building', $data['building'])->find();
            //楼不存在
            if(!$info){
                $this->error('输入不正确!');
            }
            //层号，房间号，床号存在
            if($data['floor']<0||$data['floor']>$info['floor_num'] || $data['room']<0||$data['room']>$info['room_num']  ||  $data['bed']<0||$data['bed']>$info['bed_num']){
                $this->error('输入不正确!');
            }
            
            //宿舍已分配，不能再分配   
            $count2 = db('dorm_allocated')->where(['building' => $data['building'],'floor' => $data['floor'],'room' => $data['room'],'bed' =>  $data['bed']])->count();
            if ($count2) {
                $this->error('该宿舍已被分配，不能再次分配!');
            }
                
            
            //输入正确，数据库表dorm_allocated数据更新
            $res=db('dorm_allocated')->where('username',$data['username'])->update([ 'building' => $data['building'],  'floor' => $data['floor'], 'room' => $data['room'],'bed' => $data['bed']]);
            //表dorm_total，数据不变
            if($res){
                $this->success('调换宿舍成功', url('/admin/Dormadmin/allocated'));
            }else{
                $this->error('调换宿舍失败!');
            }
        }
        
        
        $data1 = input();
        //未提交编辑修改的数据,原来的数据
        $lists = db('dorm_allocated')->where(['username' => $data1['username']])->find();
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /*
     * 删除（退宿）
     */

    public function dormdel() {
        
        $username = input('username');
        $info = db('dorm_allocated')->where('username', $username)->find();
        
        //表dorm_allocated，添加用户
        $res1 = db('dorm_allocated')->where('username', $info['username'])->delete();
        //表dorm_total，数据更新
        $res2 =db('dorm_total')->where('building',  $info['building'])->setInc('remained');
        $res3 =db('dorm_total')->where('building',  $info['building'])->setDec('allocated');
        
        if ($res1&&$res2&&$res3) {
            //db('admin_group_access')->where(['uid' => $id])->delete();
            $this->success('退宿成功', url('/admin/Dormadmin/allocated'));
        } else {
            $this->error('退宿失败', url('/admin/Dormadmin/allocated'));
        }
    }
    
    //楼层操作******************************************************************************************************
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
     * 添加（添加宿舍楼）
     */

    public function buildingadd() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            
            $data = input();
            //楼层已被分配
            $count1 = db('dorm_total')->where('building', $data['building'])->count();
            if ($count1) {
                $this->error('该楼已分配，不能再次分配!');
            }
    
            //楼层输入正确，楼层数据加入数据库
            //表dorm_total，添加楼层
            $res=db('dorm_total')->insert(['building' => $data['building'], 'floor_num' => $data['floor_num'],  'room_num' => $data['room_num'], 'bed_num' => $data['bed_num'], 'total' => $data['total'], 'allocated' => $data['allocated'], 'remained' => $data['remained']]);
            if($res){
                $this->success('添加宿舍楼成功！', url('/admin/Dormadmin/index'));
            }else{
                $this->error('添加宿舍楼失败!');
            }
        }
        return $this->fetch();
        
    }

    /*
     * 修改（编辑宿舍楼信息）
     */

    public function buildingedit() {
        
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            var_dump($data);
            //楼名不可修改，不可靠，如果改为已存在的楼名则不行
            $count1 = db('dorm_total')->where('building', $data['building'])->count();
             //var_dump($count1);
            if (!$count1) {
                $this->error('楼名不可修改!');
            }
    
            //楼层输入正确，楼层数据dorm_total数据更新
            $res=db('dorm_total')->where('building',$data['building'])->update([ 'floor_num' => $data['floor_num'],  'room_num' => $data['room_num'], 'bed_num' => $data['bed_num'], 'total' => $data['total'], 'allocated' => $data['allocated'], 'remained' => $data['remained']]);
            
            // if($res){
            //     $this->success('编辑修改宿舍楼信息成功！', url('/admin/Dormadmin/index'));
            // }else{
            //     $this->error('编辑修改宿舍楼信息失败!');
            // }
        }
        
        $data1 = input();
        //未提交编辑修改的数据,原来的数据
        $lists = db('dorm_total')->where(['building' => $data1['building']])->find();
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /*
     * 删除（删除宿舍楼）
     */

    public function buildingdel() {
        
        $building = input('building');
        //表dorm_total，删除宿舍楼,
        $res = db('dorm_total')->where('building',$building)->delete();
        if ($res) {
            $this->success('删除宿舍楼成功', url('/admin/Dormadmin/index'));
        } else {
            $this->error('删除宿舍楼失败', url('/admin/Dormadmin/index'));
        }
    }
    
    
    
    
    
    
    
}

?>