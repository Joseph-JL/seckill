<?php

/**
 *  布居
 * @file   menu.php
 * @date   2016-9-2 16:18:45
 * @author Zhenxun Du<5552123@qq.com>
 * @version    SVN:$Id:$
 */

namespace app\admin\widget;

class Menu {

    public function __construct() {
        if (!session('user_id')) {
            return false;
        }
    }

    public function index() {
        $menu = model('Menu')->getMyMenu(1);

        $menuTree =$this->list_to_tree($menu);

        trace($menuTree);

        $html = '<ul id="nav">';
        $html .=$this->menu_tree($menuTree);
        $html .= "
                </ul>";
        //echo $html;exit;
        return $html;
    }
    
    public function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parentid', $child = '_child') {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = 0;
            if (isset($data[$pid])) {
                $parentId = $data[$pid];
            }
            if ((string) $root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

    private function menu_tree($tree) {

        $html = '';

        if (is_array($tree)) {

            foreach ($tree as $val) {

                if (isset($val["name"])) {
                    $title = $val["name"];

                    if (!empty($val["a"])) {
                        $url = url('@admin/' . $val['c'] . '/' . $val['a']);
                    }

                    if (empty($val['icon'])) {
                        $icon = "&#xe6a7;";
                    } else {
                        $icon = "&#".$val['icon'].";";
                    }

                    if ($val['c'] == request()->controller() && $val['a'] == request()->action()) {
                        $current = 'current';
                    } else {
                        $current = '';
                    }

                    $opened ='';

                    if (isset($val['_child'])) {
                        if($this->check_menu_open($val['_child']))
                            $opened ='opened';
                        $html.=' 
                            <li class="list">
                                <a href="javascript:;">
                                    <i class="iconfont">' . $icon . '</i>
                                    ' . $title . '
                                    <i class="iconfont nav_right">&#xe697;</i>
                                </a>
                                <ul class="sub-menu '.$opened.' ">
                            ';

                        $html.=$this->menu_tree($val['_child']);

                        $html.='              
                            </ul>
                        </li>
                        ';
                    } else {

                        $html.='
                            <li class="'.$current.'">
                            <a href = "' . $url . '">
                            <i class="iconfont">' .  $icon. '</i>
                            ' .  $title . '
                              
                            </a>
                            </li>
                            ';
                    }
                }
            }
        }

        return $html;
    }
    
    private function check_menu_open($tree){
        if (is_array($tree)) {
            foreach ($tree as $val) {
                if ($val['c'] == request()->controller() && $val['a'] == request()->action()) {
                    return true;
                }
            }
        }
        return false;
    }

}