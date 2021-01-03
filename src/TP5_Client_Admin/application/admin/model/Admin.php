<?php


namespace app\admin\model;

use think\Db;

class Admin extends \think\Model {

    public $status = array(1 => '无效', 2 => '有效');

    /**
     * 登录时调用
     * @param String $username 用户名
     * @return Array
     */
     //查询用户名是否存在
    public function getInfoByUsername($username) {
        $res = db('admin_user')->field('id,username,password')
            ->where(array('username' => $username))
            ->find();
            
        return $res;
    }

    /**
     *
     * @param int $userid 用户ID
     * @return Array
     */
     //根据用户id查询组id，可能属于多个组，返回值为字符串，id之间用逗号隔开
    public function getUserGroups($uid) {

        $res = db('admin_group_access')->field('group_id')->where('uid', $uid)->select();

        $userGroups = '';
        if ($res) {
            foreach ($res as $k => $v) {
                $userGroups .= $v['group_id'] . ',';
            }
            return trim($userGroups, ',');
        } else {
            return false;
        }
    }

    /**
     * 登陆更新
     * @param int $id id
     * @param array $data 更新的数据
     */
    public function editInfo( $id, $data = array()) {
        $data['lastlogintime'] = time();
        $data['lastloginip'] = ip2long(request()->ip());
        
        $res=db('admin_user')->where('id',$id)->update([ 'lastlogintime' => $data['lastlogintime'],  'lastloginip' => $data['lastloginip']]);
        return $res;
    }

}
