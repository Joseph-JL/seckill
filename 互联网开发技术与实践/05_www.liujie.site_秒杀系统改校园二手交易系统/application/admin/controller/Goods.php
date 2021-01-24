<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 商品种类设置
 */
class Goods extends Common
{
    /**
     * index/info 查看商品种类
     */
    public function index()
    {
        $lists = db('goods_class')->field('id,goodsName,totalNum,distributedNum,remainedNum,seckillStartTime')->paginate(25);
        $page = $lists->render();//页数显示
        
        $this->assign('lists', $lists); 
        $this->assign('page', $page); 
        
        return $this->fetch();
    }
    
    /**
     * add 添加商品种类
     */
    public function add()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();

            $count = db('goods_class')->where('goodsName', $data['goodsName'])->find();
            if ($count) {
                    $this->error('该商品已存在');
                }

            //更新goods_class表
            $res=db('goods_class')->insert(['goodsName' => $data['goodsName'], 'totalNum' => $data['totalNum'],  'distributedNum' => $data['distributedNum'],'remainedNum' => $data['remainedNum']]);
            if($res){
                return $this->success('添加商品种类成功!','Goods/index');
            }else{
                return $this->error('添加商品种类失败!','Goods/index');
            }
        }
        return $this->fetch();
    }
    
    /**
     * edit 编辑商品种类、可管理权限
     * ▲id不可修改，因为使用id作为索引
     * 只能修改goodsName、totalNum、remainedNum;
     * distributedNum不可修改
     */
    public function edit()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            
            //▲id不可修改，需用Javascript限制输入
            
            //有修改才更新
            $goods=db('goods_class')->where('id', $data['id'])->field('id,goodsName,totalNum,distributedNum,remainedNum')->find();
            
            if($goods['id']==$data['id']&&$goods['goodsName']==$data['goodsName']&&$goods['totalNum']==$data['totalNum']&&$goods['distributedNum']==$data['distributedNum']&&$goods['remainedNum']==$data['remainedNum']){
                $this->error('提交未作修改','Goods/index');
            }
            //distributedNum不可修改,totalNum、remainedNum同步
            if($goods['distributedNum']!=$data['distributedNum']){
                $this->error('distributedNum不可修改！','Goods/index');
            }
            $val1=(int)$data['totalNum']-(int)$goods['totalNum'];
            $val2=(int)$data['remainedNum']-(int)$goods['remainedNum'];
            if($val1!=$val2){
                $this->error('totalNum、remainedNum修改不同步！','Goods/index');
            }
            
            //修改的name不能与除去自身的已有name重复
            $goods1= db('goods_class')->where(array('id' => array('NEQ', $data['id'])))->select();
            foreach($goods1 as $key=>$val){
                if($val['goodsName']==$data['goodsName']){
                    $this->error('该商品名已存在','Goods/index');
                }
            }
            
            //更新goods_class表
            $res=db('goods_class')->where('id',$data['id'])->update(['goodsName' => $data['goodsName'],  'totalNum' => $data['totalNum'],'remainedNum' => $data['remainedNum']]);
            if($res){
                return $this->success('更新商品种类成功！','Goods/index');
            }else{
                return $this->error('更新商品种类失败！','Goods/index');
            }
        }
        
        $data1=input();
        $lists = db('goods_class')->where('id', $data1['id'])->field('id,goodsName,totalNum,distributedNum,remainedNum')->find();
        $this->assign('lists', $lists); 
        return $this->fetch();
    }
    
    /**
     * del 删除商品种类
     * 如果商品没有被分配才可以删除
     */
    public function del()
    {   
        $id=input('id');
        //如果商品被分配了
        $res1=db('goods_detail')->where(['goodsClassID' => $id,'isDistributed' => 1])->find();
        if($res1){
            return $this->error('该商品种类被分配给用户了,不能删除!','Goods/index');
        }else{//不持有商品的顾客，支持删除
            $res2=db('goods_class')->where('id', $id)->delete();
            if($res2){
                return $this->success('商品种类删除成功!','Goods/index');
            }else{
                return $this->error('商品种类删除失败!','Goods/index');
            }
        }
    }
    
    /**
     * detail 商品详情
     */
    public function detail()
    {
        $goodsClassID=input('id');
        
        $lists = db('goods_detail')->where('goodsClassID',$goodsClassID)->field('id,goodsNo,goodsClassID,isDistributed,clientName')->paginate(25);
        $page = $lists->render();//页数显示
        
        $count= db('goods_detail')->where('goodsClassID', $goodsClassID)->count();//查询记录总数
        $goodsclass= db('goods_class')->where('id',$goodsClassID)->field('goodsName')->find();//商品种类
        $goodsName=$goodsclass['goodsName'];
        
        $this->assign('lists', $lists);
        $this->assign('page', $page); 
        $this->assign('count',$count);
        $this->assign('goodsclass',$goodsName);
        return $this->fetch();
    }
    
    /**
     * time 设置秒杀开始时间
     */
    public function time()
    {
        $dosubmit=isset($_POST["dosubmit"])?$_POST["dosubmit"]:0;
        if($dosubmit) {
            $data = input();
            //检查string时间格式

            //更新goods_class表
            $res=db('goods_class')->where('id', $data['id'])->update(['seckillStartTime' => $data['seckillStartTime']]);
            if($res){
                return $this->success('设置秒杀开始时间成功!','Goods/index');
            }else{
                return $this->error('设置秒杀开始时间失败!','Goods/index');
            }
        }
        
        
        $goodsclassID=input('id');
        $lists = db('goods_class')->where('id', $goodsclassID)->field('id,seckillStartTime')->find();
        $this->assign('lists', $lists); 
        return $this->fetch();
    }
    
}