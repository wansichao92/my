<?php
/**
 * 获取openid
 * wansichao 2019-06-22
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//调用方法
$openid = $weixin->getOpenId();
echo '你的openid是：'.$openid;
