<?php
namespace app\client\controller;

use think\Controller;

/**
 * 商品卖出
 */
class Sell extends Common
{
     /**
     * index 出售商品发布
     */
    public function index()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data=input();
            $saletime=time();
            $salername=session('username');
            $saler=db('client_user')->where('username', $salername)->field('email')->find();
            
            $res=db('goods')->insert(['goodsname' => $data['goodsname'],'description' => $data['description'],'price' => $data['price'],'saletime' => $saletime,'salername' => $salername,'saleremail' => $saler['email']]);
            
            if($res){
                return $this->success('成功发布商品!','Buy/index');
            }else{
                return $this->error('发布商品失败!','Sell/index');
            }
        }
        return $this->fetch();
    }
    
    /**
     * mysell 我发布的
     */
    public function mysell()
    {
        $salername=session('username');
        $lists = db('goods')->where('salername', $salername)->field('id,goodsname,description,price,status,listorder,saletime,buytime,salername,saleremail,buyername,buyeremail,dealstatus')->select();
        
        
        $i=1;
        foreach($lists as $key=>$val){//查询管理员用户角色
            $lists[$key]['No']=$i;
            $i=$i+1;
            
            $sellstatus="";
            if($val['status']==0){
                $sellstatus="未卖出";
            }else{
                $sellstatus="已卖出";
            }
            $lists[$key]['sellstatus']=$sellstatus;
        }
        $this->assign('lists', $lists);   
        return $this->fetch();
    }
    
    /**
     * dealstatus 更改交易状态，交易完成
     */
    public function dealstatus()
    {
        $goodsid=input('goodsid');
        $res=db('goods')->where('id', $goodsid)->update(['dealstatus' => "交易成功，无需确认！"]);
        if($res){
            return $this->success('交易确认成功!','Buy/mybuy');
        }else{
            return $this->error('请重新确认!','Buy/mybuy');
        }
    }
}