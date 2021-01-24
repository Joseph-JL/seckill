<?php
namespace app\index\controller;

use think\Controller;
    
class Redis extends Controller 
{
    public function index()
    {
        echo "redis_test";
    }
    public function test1()
    {
        $redis = new \Redis();
    	$redis->connect('127.0.0.1', 6379);
        // $redis->auth('password'); # 如果没有密码则不需要这行
     
    	//把 'test'字符串存入 redis
        $redis->set('test_name', 'test');
        // 把 'test_name' 的 值从 redis 读取出来 
        echo $redis->get('test_name');
    }
    public function test_inc()
    {
        $redis = new \Redis();
    	$redis->connect('127.0.0.1', 6379);
        $redis->incr("username");
        echo $redis->get("username");
    }
    public function test_dec()
    {
        $redis = new \Redis();
    	$redis->connect('127.0.0.1', 6379);
        $redis->decr("username");
        echo $redis->get("username");
    }
    public function test_settimeout()
    {
        $redis = new \Redis();
    	$redis->connect('127.0.0.1', 6379);
        $redis->incr("username2");
    	$redis->expire("username2",20);  //设置过期时间，单位秒
        echo $redis->get("username2");
    }
    public function test_login()
    {
        //此处如何实现连续尝试三次出现验证码？
        return $this->fetch();
    }
    public function dologin()
    {
        //获取用户提交的用户名和密码
        $username = input('username');
        $password = input('password');
        //用户存在数据库的密码
        $pwd = '123456';
 
        $redis = new \Redis();
    	$redis->connect('127.0.0.1', 6379);
        $num = $redis->get($username);
        if($num > 5){
            echo '登录次数超过5次,请5分钟后再尝试';
            exit();
        }
 
        if($password != $pwd){
            //登录失败进行计数操作，key为用户名这里认为是唯一值，value就是错误次数
            $redis->incr($username);
            $redis->expire($username,20);  //设置过期时间，单位秒
            echo '登录失败';
        }else{
            echo '登录成功';
        }
    }
}