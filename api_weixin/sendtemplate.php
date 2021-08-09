<?php
/**
 * 消息模版
 * wansichao 2019-06-19
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//装配群发内容
$open_id = 'oDMjn1LeYpcrMPor3iuEkI1p9_qw';
$template_id = 1;
$data = array(
	'first'    => '秋游',
	'keyword1' => date('Y-m-d'),
	'keyword2' => '江西南昌',
	'keyword4' => '注意安全',
	'remark'   => '测试...aaa',
);
//调用方法
$res = $weixin->sendTemplate($open_id, $template_id, $data);
var_dump($res);
