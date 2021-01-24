<?php
namespace app\admin\controller;
use think\Controller;
use think\Paginator;

/**
 * 订单记录
 */
class Order extends Common
{
    /**
     * index 查看抢购订单
     */
    public function index()
    {
        $lists = db('order_record')->field('id,goodsClassID,goodsNo,clientName,finishTime')->paginate(25);
        $page = $lists->render();//页数显示
        
        $this->assign('lists', $lists);
        $this->assign('page', $page); 
        
        $count= db('order_record')->count();//查询记录总数
        $this->assign('count', $count);
        
        $goodsClassID=$lists[0]['goodsClassID'];//▲此处只设置了只能抢购ID为1的商品，所以商品此处商品种类一定，有待改进
        $goodsclass= db('goods_class')->where('id',$goodsClassID)->field('goodsName')->find();//商品种类
        $goodsName=$goodsclass['goodsName'];
        $this->assign('goodsclass',$goodsName);
        
        return $this->fetch();
    }
    
}