<?php
namespace app\client\controller;
use think\Loader;

use think\Controller;

class Result extends Common
{
    public function index()
    {
        $username=session("username"); 
        $info = db('goods_detail')->where('clientName', $username)->find();
        if(!$info){
            $this->error('未抢购到该商品,请秒杀!','Index/index');
        }else{
            return $this->fetch();
        }
    }
}

?>