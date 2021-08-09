<?php
/**
 * 设置公众号底部菜单
 * wansichao 2019-06-14
 * wansichao920510@qq.com
 * 
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
//自定义菜单
$menu = array();
$menu['button'] = array(
	//第一个菜单
	array(
		'name' => urlencode('获取id'),
		'type' => 'view',
		'url'  => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxec936f771d0f4d17&redirect_uri=http://129.28.98.199/weixin/getopenid.php&response_type=code&scope=snsapi_base&state=123#wechat_redirect',
	),
	//第二个菜单
	array(
		'name' => urlencode('菜单2'),
		'sub_button' => array(
			//子菜单1
			array(
				'name' => urlencode('opeid'),
				'type' => 'view',
				'url'  => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxec936f771d0f4d17&redirect_uri=http://129.28.98.199/weixin/getopenid.php&response_type=code&scope=snsapi_base&state=123#wechat_redirect',
			),
			//子菜单2
			array(
				'name' => urlencode('info'),
				'type' => 'view',
				'url'  => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxec936f771d0f4d17&redirect_uri=http://129.28.98.199/weixin/getuserinfo.php&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect',
			),
		),
	),
	//第三个菜单
	array(
		'name' => urlencode('菜单3'),
		'type' => 'click',
		'key'  => 'ts_imgtext',
	),
);
//调用方法
$menu = $weixin->menu($menu);
var_dump($menu);
