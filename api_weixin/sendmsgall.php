<?php
/**
 * 设置群发
 * wansichao 2019-06-19
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//装配群发内容
$open_id = 'oDMjn1LeYpcrMPor3iuEkI1p9_qw';
$content = '群发短信内容...';
//调用方法
$res = $weixin->sendMsgAll($open_id, $content);
var_dump($res);
