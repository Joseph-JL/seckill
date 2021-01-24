<?php
namespace app\admin\controller;
use think\Loader;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        $info = db('crm_user')->select();
        //var_dump($info);
        $this->assign('lists', $info);
        return $this->fetch();
    }
}

?>