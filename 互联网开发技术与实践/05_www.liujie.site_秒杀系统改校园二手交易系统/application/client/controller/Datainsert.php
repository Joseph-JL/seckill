<?php
namespace app\client\controller;
use think\Loader;

use think\Controller;

class Datainsert extends Controller
{
    public function index()
    {
        for($i=26;$i<=1000;$i++){
            //$i2str=(string)$i;
            
            db('goods_detail')->insert(['goodsNo' => $i, 'goodsClassID' => 1,  'isDistributed' => 0 ]);
            
            
            
        }
        $this->success('未抢购到该商品,请秒杀!','Login/index');
    }
    
    public function clientuser()
    {
        for($i=6;$i<=1000;$i++){
            //$i2str=(string)$i;
            $username='clientuser'.$i;
            $password='clientuser'.$i;
            $email='clientuser'.$i.'@qq.com';
            
            db('client_user')->insert(['id' => $i, 'username' => $username,  'password' => $password ,  'email' => $email]);
        }
        $this->success('未抢购到该商品,请秒杀!','Login/index');
    }
}

?>