<?php 
session_start();
$appId     = "wx68d1c69880b3a5cb";
$appSecret = "d508dbc8e947c629eb34c1db369b1c4a";

class wxscan 
{
    public function get_access_token()
    {
		$data = json_decode($this->get_php_file("access_token.php"));
		if ($data->expire_time<time()) {
		    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
		    $res = json_decode($this->httpGet($url));
		    $access_token = $res->access_token;
		    if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $this->set_php_file("access_token.php", json_encode($data));
		    }
		} else {
		  $access_token = $data->access_token;
		}
		return $access_token;
    }
    
    private function get_php_file($filename) 
    {
		return trim(substr(file_get_contents($filename), 15));
    }
    
    private function set_php_file($filename, $content) 
    {
		$fp = fopen($filename, "w");
		fwrite($fp, "<?php exit();?>" . $content);
		fclose($fp);
	}
    
    public function make_signature($nonceStr, $timestamp, $jsapi_ticket, $url)
	{
		$tmpArr = array(
			'noncestr'     => $nonceStr,
			'timestamp'    => $timestamp,
			'jsapi_ticket' => $jsapi_ticket,
			'url'          => $url
        );
        
		ksort($tmpArr, SORT_STRING);
		$string1 = http_build_query($tmpArr);
		$string1 = urldecode($string1);
        $signature = sha1($string1);
        
		return $signature;
	}

    public function http_curl($url, $type='get', $res='json', $arr='')
    {
		$ch  =curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($type == 'post') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        $output =curl_exec($ch);
        curl_close($ch);
        
        return $output;
	}
}

$wxscan = new wxscan();
$accesstoken = $wxscan->get_access_token();
if ($_SESSION['tickets'] && $_SESSION['expires_ins']>time()) {
	$jsapi_ticket = $_SESSION['tickets'];
} else {
	$QjAccessToken       = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accesstoken.'&type=jsapi';
    $QjAccessTokenValue  = $wxscan->http_curl($QjAccessToken,$type='get',$res='json',$arr='');
    $QjAccessTokenValue  = json_decode($QjAccessTokenValue,true);
	$_SESSION['tickets'] = $QjAccessTokenValue['ticket'];
	$jsapi_ticket        = $_SESSION['tickets'];
	$_SESSION['expires_ins'] = time()+7200;
}


$nonceStr  = 'jiazuqian';	
$timestamp = time();
$url       = 'http://erp.appjx.cn/ceshi.html';
$signature = $wxscan->make_signature($nonceStr, $timestamp, $jsapi_ticket, $url);
header('Content-Type:application/json; charset=utf-8');
exit(json_encode(array('appid'=>$appId, 'timestamp'=>$timestamp, 'nonceStr'=>$nonceStr, 'signature'=>$signature)));
?>
