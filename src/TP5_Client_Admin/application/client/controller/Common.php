<?php

/**
 * 后台公共文件
 */

namespace app\client\controller;

use think\Controller;

class Common extends Controller {

    public function __construct(\think\Request $request = null) {

        parent::__construct($request);

        //检测session是否有效
        if (!session('username')) {
            $this->error('请登陆', 'login/index');
        }
    }
}