<?php
/**
 * 微信公众号开发接口验证
 * wansichao 2019-06-14
 * wansichao920510@qq.com
 * 
 * 开发者通过检验signature对请求进行校验。
 * 若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，成为开发者成功，否则接入失败。
 *
 * 加密/校验流程如下：
 * 1）将token、timestamp、nonce三个参数进行字典序排序 
 * 2）将三个参数字符串拼接成一个字符串进行sha1加密 
 * 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
 *
 */
include 'wenxin.class.php';
$weixin = new Weixinapi();
$token     = 'wansichao';
$nonce     = $_GET['nonce'];
$timestamp = $_GET['timestamp'];
$echostr   = $_GET['echostr'];
$signature = $_GET['signature'];
//形成数组，然后按字典序排序
$array = array();
$array = array($nonce, $timestamp, $token);
sort($array);
//拼接成字符串,sha1加密 ，然后与signature进行校验
$str = sha1(implode($array));
if ($str == $signature && $echostr) {
    //第一次接入weixin api接口的时候
    echo $echostr;
    exit;
} else {
	//推送消息
	$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
    $postObj = simplexml_load_string($postArr);
    //事件推送
    if (strtolower($postObj->MsgType) == 'event') {
        //subscribe关注事件
        if (strtolower($postObj->Event) == 'subscribe') {
            $text = '欢迎来到万思超的小站！';
            $info = $weixin->tsText($postObj, $text);
            echo $info;
        }
    }

    //判断该数据包是否是文本消息
    if (strtolower($postObj->MsgType) == 'text') {
        //设置关键字自动回复
        switch (trim($postObj->Content))
        {
            case '你好':
                $autocontent  = '你好，请问有什么能帮助你的吗？';
            break;  
            case '哈哈':
                $autocontent  = '傻笑什么？';
            break;
            case '图文':
                $tuwen  = true;
            break;
            case '天气':
                $tianqi  = true;
            break;
            default:
                $autocontent  = '输入“你好”或者“哈哈”关键字有惊喜哦~';
        }
        
        //单文本回复
        if ($autocontent) {
            $info = $weixin->tsText($postObj, $autocontent);
            echo $info;
        }

        //图文回复
        if ($tuwen) {
            $title = '百度';
            $description = '百度江西';
            $picur = 'https://www.baidu.com/img/dong_074d421391cb1506ebb3744155d2a809.gif';
            $url = 'https://www.baidu.com/';
            $info = $weixin->tsImgText($postObj, $title, $description, $picur, $url);
            echo $info;
        }

        //回复南昌天气
        if ($tianqi) {
            //调用天气接口
            //$tianqiapi = '88032c056dd21154317c9620fec48834';
            $tianqiapi = '4fe12c64626b9dbecc45cc19cb1839ec';
            $url = 'http://apis.juhe.cn/simpleWeather/query?city=南昌&key='.$tianqiapi;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch);
			if (curl_errno($ch)) {
				var_dump(curl_errno($ch));
			}
			curl_close($ch);
			//返回南昌天气数据
			$nctianqi = json_decode($res, true);

			$toUser   = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $time     = time();
            $msgType  = 'text';
            $content  = $nctianqi['result']['city'].'天气'."\n".
            			'更新时间：'.date("Y-m-d H:i:s")."\n"."\n".
            			$nctianqi['result']['realtime']['info']."\n".
            			'气温'.$nctianqi['result']['realtime']['temperature'].'°C'."\n".
            			'降水率'.$nctianqi['result']['realtime']['humidity'].'%'."\n".
            			//$nctianqi['result']['realtime']['wid']."\n".
            			$nctianqi['result']['realtime']['direct'].$nctianqi['result']['realtime']['power']."\n";
            $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
            $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            echo $info;
        }
    }
}