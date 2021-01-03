<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 菜单设置
 */
class Menu extends Common
{
    /**
     * index 查看菜单结构表
     */
    public function index()
    {
        $lists = db('admin_menu')->field('id,name,parentid,icon,c,a,listorder,display')->select();
        $this->assign('lists', $lists);   
        return $this->fetch();
    }
    
}