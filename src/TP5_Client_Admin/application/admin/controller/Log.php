<?php
namespace app\admin\controller;
use think\Controller;
use think\Paginator;

/**
 * 日志设置
 */
class Log extends Common
{
    /**
     * index 查看日志
     */
    public function index()
    {
        $lists = db('admin_log')->field('id,m,c,a,querystring,userid,username,ip,time')->paginate(25);
        $page = $lists->render();//页数显示
        
        $items = $lists->items();
        foreach($items as $key=>$val){
            $url=$val['m'].'/'.$val['c'].'/'.$val['a'].'/'.$val['querystring'];
            $items[$key]['url']=$url;
        }
        $this->assign('lists', $items);
        $this->assign('page', $page); 
        
        $count= db('admin_log')->count();//查询记录总数
        $this->assign('count', $count);
        
        return $this->fetch();
    }
    
}