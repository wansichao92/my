<?php
/**
 * 获取微信用户详细信息
 * wansichao 2019-06-22
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//调用方法
$userinfo = $weixin->getUserInfo();
print_r($userinfo);
