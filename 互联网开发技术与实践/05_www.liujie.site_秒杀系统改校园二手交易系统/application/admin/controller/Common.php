<?php

/**
 * 后台公共文件
 */

namespace app\admin\controller;

use think\Controller;

class Common extends Controller {
    /**
     * 所有页面打开时，都要运行
     */
    public function __construct(\think\Request $request=null){
        parent::__construct($request);
        
        //检测是否登录，session是否有效
        if(!session('user_id')){
            $this->error("请登录！","Login/index");
        }
        
        //权限检查
        $user_id=session('user_id');
        if(!$this->_checkAuthor($user_id)){
            $this->error("你无权操作！");
        }
        
        //记录操作日志
        $this->_addLog();
    }
    
    
    /**
     * 权限检查
     */ 
    private function _checkAuthor($user_id){
        
        //系统管理员所有可操作
        $group_id=session('group_id');
        if($group_id==1){
            return true;
        }
        
        $c=strtolower(request()->controller());
        $a=strtolower(request()->action());
        
        //缺省、Index/index页面所有登录用户可访问
        if (preg_match('/^public_/', $a)) {
            return true;
        }
        if ($c == 'index' && $a == 'index') {
            return true;
        }
        
        //$menu为有权限访问的menu表记录，数组
        $menu=model("Menu")->getMyMenu();
        foreach ($menu as $key=>$val){
            //遍历，要访问的控制器和方法，与有权访问的，一个比对成功即可
            if(strtolower($val['c'])==$c&&strtolower($val['a'])==$a){
                return true;
            }
        }
        return false;
    }
    
    
    
    /**
     * 记录日志,在点击进入页面时记录操作
     */ 
     private function _addLog(){
         $data=array();
         
         //url
         $data['m']=request()->module();//模块
         $data['c']=request()->controller();//控制器
         $data['a']=request()->action();//方法
         $data['querystring']=request()->query()?  '?'.request()->query() : '';//url后面的参数
         
         //user
         $data['userid']=session('user_id');
         $data['username']=session('user_name');
         
         //extra
         $data['ip']=ip2long(request()->ip());
         $data['time']=time();
         
         $arr=array('Index/index','Log/index');
         //访问'Index/index','Log/index'以外的页面才记录日志
         if(!in_array($data['c'].'/'.$data['a'],$arr)){
             db('admin_log')->insert($data);
         }        
         
         
     }
    
    
}