<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $username=session('user_name');
        $this->assign('username', $username);
        
        return $this->fetch();
    }
}
