<?php
namespace app\client\controller;
use think\Loader;

use think\Controller;

class Seckill extends Common
{   
    //填写秒杀信息页面
    public function index()
    {
        $goodsclass=db('goods_class')->where('id', 1)->find();
        $seckillStartTime=$goodsclass['seckillStartTime'];//给变量$time2获取秒杀开始时间 //易错：统一采用双位2021-01-02 00:00:00
        $time1="1970-01-01 00:00:00";
        $timeSec=strtotime($seckillStartTime)-strtotime($time1);
        
        $this->assign('timeSec',$timeSec);
        $this->assign('seckillStartTime',$seckillStartTime);
        
        
        $username=session('username');
        $this->assign('username',$username);
        
        return $this->fetch();
    }
    
    
    //验证码数据前端验证
    public function codecheck(){
        $inputcode=input("checkcode");//输入的验证码
        
        if(!captcha_check($inputcode)){//图片真实验证码
            return json("验证码输入错误，请重新输入！");
        }else{
            return json("验证码输入正确,请提交！");
        }
    }
    
    //到秒杀时间才传URL
    public function formURL(){
        date_default_timezone_set('PRC'); 
        $time1=date("Y-m-d H:i:s");//获取当前时间
        
        $goodsclass=db('goods_class')->where('id', 1)->find();
        $time2=$goodsclass['seckillStartTime'];//给变量$time2获取秒杀开始时间 //易错：统一采用双位2021-01-02 00:00:00
        
        if(strtotime($time1)>=strtotime($time2)){
            return json("/client/Seckill/doseckill");
        }else{
            return json("#");
        }
    }
    
    /**
     * 提交已填秒杀表单信息 处理
     */
    public function doseckill(){
        //未到系统开放时间，不处理表单请求
        $time1=date("Y-m-d H:i:s");//获取当前时间
        
        $goodsclass=db('goods_class')->where('id', 1)->find();
        $time2=$goodsclass['seckillStartTime'];//给变量$time2获取秒杀开始时间 //易错：统一采用双位2021-01-02 00:00:00
        if(strtotime($time1)<strtotime($time2)){
            $this->error('系统尚未开放，请等开放后再秒杀!','Seckill/index');
        }
        
        //验证当前用户名与表单输入用户名一致
        $username = input('username');
        $usernameSession=session('username');
        if($username!=$usernameSession){
            $this->error('用户名错误','Seckill/index');
        }
        
        //验证输入验证码正确
        $inputcode=input("checkcode");//输入的验证码
        if(!captcha_check($inputcode)){//图片真实验证码
            $this->error('验证码错误','Seckill/index');
        }
        
        //输入正确,提交到go订单请求
        else{
            //$this->redirect('http://101.200.78.125:10000/api/room/order?username=clientuser3',['username' => $username]);
            //header('location:http://101.200.78.125:10000/api/room/order?username='.$username);exit;
            //$this->success('验证码正确','Seckill/index');
            $url="http://101.200.78.125:10000/api/room/order?username=".$username;
            $ret=file_get_contents($url);
            //echo $ret['data'];
            if($ret=='{"code":200,"data":"提交成功"}' ){
                $this->success('提交成功，请查看秒杀结果!','Index/index');
            }else{
                $this->success('提交失败，请重新秒杀!','Seckill/index');
            }
        }
    }



}