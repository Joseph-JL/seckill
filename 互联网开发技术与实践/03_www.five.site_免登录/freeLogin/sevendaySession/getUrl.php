<?php
 
// php 获取当前访问的完整url
function GetCurUrl() {
    $url = 'http://';
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $url = 'https://';
    }
     
    // 判断端口
    if($_SERVER['SERVER_PORT'] != '80') {
        $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . ':' . $_SERVER['REQUEST_URI'];
    } else {
        $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['REQUEST_URI'];
    }
     
    return $url;
}

$url=GetCurUrl();

echo $url;

?>