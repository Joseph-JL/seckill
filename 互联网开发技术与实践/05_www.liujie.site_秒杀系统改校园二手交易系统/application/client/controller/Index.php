<?php
namespace app\client\controller;
use think\Loader;

use think\Controller;

class Index extends Common
{
    public function index()
    {
        $info = db('goods_class')->select();
        //var_dump($info);
        $this->assign('lists', $info);
        return $this->fetch();
    }
}

?>