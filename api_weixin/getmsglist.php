<?php
/**
 * 获取聊天记录
 * wansichao 2019-06-26
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//获取聊天记录参数
$arr = array(
	'starttime' => 1561507200, //开始时间戳
	'endtime'   => 1561509677, //结束时间戳 区间不能超过24小时
	'msgid'     => 1,          //起始id
	'number'    => 1000	       //最大条数 10000
);
//调用方法
$res = $weixin->getMsgList($arr);
var_dump($res);
