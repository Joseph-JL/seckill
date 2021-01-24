<?php
namespace app\client\controller;

use think\Controller;

/**
 * 商品购买
 */
class Buy extends Common
{
    /**
     * index 待抢购商品详情
     * 商品如果已卖出，则不再显示；只在交易双方发布和购买下显示
     */
    public function index()
    {
        $lists = db('goods')->where('status', 0)->field('id,goodsname,description,price,status,listorder,saletime,buytime,salername,saleremail,buyername,buyeremail')->select();
        
        $i=1;
        foreach($lists as $key=>$val){//查询管理员用户角色
            $lists[$key]['No']=$i;
            $i=$i+1;
            $lists[$key]['dealstatus']="正在热卖";
        }
        $this->assign('lists', $lists);   
        return $this->fetch();
    }
    
    /**
     * buy 购买
     */
    public function buy()
    {
        $goodsid=input('goodsid');
        $buytime=time();
        $buyername=session('username');
        $buyer=db('client_user')->where('username', $buyername)->field('email')->find();
        
        $res=db('goods')->where('id', $goodsid)->update(['status' => 1,'buytime' => $buytime,'buyername' => $buyername,'buyeremail' => $buyer['email']]);
        
        if($res){
            return $this->success('成功购买商品!','Buy/mybuy');
        }else{
            return $this->error('购买商品失败!','Buy/index');
        }
    }
    
    /**
     * mybuy 我购买的
     */
    public function mybuy()
    {
        $buyername=session('username');
        $lists = db('goods')->where('buyername', $buyername)->field('id,goodsname,description,price,status,listorder,saletime,buytime,salername,saleremail,buyername,buyeremail,dealstatus')->select();
        
        $i=1;
        foreach($lists as $key=>$val){//查询管理员用户角色
            $lists[$key]['No']=$i;
            $i=$i+1;
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