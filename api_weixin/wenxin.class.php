<?
class Weixinapi
{
	//订阅号appid,appsecret
	public $appid = "wxaecdd1154479596a";
	public $appsecret = "0901ee724710868182a2e824178b7233";
	//开发测试帐号appid,appsecret
	public $appid_cs = 'wx2ca48bc4e302506f';
	public $appsecret_cs = '9c25faaaabe8e5c38d803d92b7e0af5e';
	
	//获取微信access_token
	public function getAccessToken($appid, $appsecret)
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($ch);
		if (curl_errno($ch)) {
			var_dump(curl_errno($ch));
		}
		curl_close($ch);
		$accesstoken = json_decode($res, true);
		return $accesstoken['access_token'];
	}

	/**
	 * CURL方法封装
	 *
	 * $url  string 接口url
	 * $type string 请求类型
	 * $res  string 返回数据类型
	 * $arr  string post请求参数 
	 */
	public function http_curl($url, $type='get', $res='json', $arr='')
	{
		//1.初始化curl
		$ch = curl_init();
		//2.设置curl参数
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($type == 'post') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		}
		//3.采集
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			return curl_errno($ch);
		} else {
			//4.关闭
			curl_close($ch);
			if ($res == 'json') {
				return json_decode($output, true);
			}
		}
	}

	//access_token存入session
	public function getWxAccessToken()
	{
		//开发测试帐号appid,appsecret
		$appid_cs = 'wx2ca48bc4e302506f';
		$appsecret_cs = '9c25faaaabe8e5c38d803d92b7e0af5e';
		if ($_SESSION['access_token'] && $_SESSION['expire_time']>time()) {
			return $_SESSION['access_token'];
		} else {
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid_cs.'&secret='.$appsecret_cs;
			$res = $this->http_curl($url, 'get', 'json');
			if ($res) {
				$access_token = $res['access_token'];
				//将重新获取的access_token存入session
				$_SESSION['access_token'] = $access_token;
				$_SESSION['access_token'] = time()+7000;
				return $access_token;
			}
		}
	}

	//被动推送文本
	public function tsText($postObj, $text)
	{
		$toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  = 'text';
        $content  = $text;
        $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        return $info;
	}

	//被动推送图文
	public function tsImgText($postObj, $title, $description, $picur, $url='')
	{
		$toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  = 'text';
        $content  = array(
		                'title' => $title,
		                'description' => $description,
		                'picur' => $picur,
		                'url' => $url,
		            );
        $template = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[".$content['title']."]]></Title>
                    <Description><![CDATA[".$content['description']."]]></Description>
                    <PicUrl><![CDATA[".$content['picur']."]]></PicUrl>
                    <Url><![CDATA[".$content['url']."]]></Url>
                    </item>
                    </Articles>
                    </xml>";
        $info = sprintf($template, $toUser, $fromUser, $time, 'news');
        return $info;
	}

	//被动推送天气
	public function tsWeather($postObj, $content)
	{
		
	}

	//菜单接口
	public function menu($menu)
	{
		header('content-type:text/html;charset=utf-8');
		$access_token = $this->getWxAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		//转化为json格式
		$postjson = urldecode(json_encode($menu));
		$res = $this->http_curl($url, 'post', 'json', $postjson);
		return $res;
	}

	//群发(预览)接口
	public function sendMsgAll($open_id, $content)
	{
		$access_token = $this->getWxAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
		$data = array(
			'touser'  => $open_id, //用户openId
			'text'    => array('content'=>urlencode($content)), //文本内容
			'msgtype' => 'text', //消息类型
		);
		$datajson = urldecode(json_encode($data));
		$res = $this->http_curl($url, 'post', 'json', $datajson);
		return $res;
	}

	//消息模版接口
	public function sendTemplate($open_id, $template_id, $data)
	{
		$access_token = $this->getWxAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
		//模版1 通知公告
		$template1 = array(
			'touser' => $open_id, //用户openId
			'template_id' => 'bliSCwgWmIdQVbLNjp-n1C2d4ouIi3LrgxNTPye6xuo', //模版编号
			//'url' => $url, //跳转地址
			'data' => array(
				'first'    => array('value'=>$data['first'], 'color'=>'#FF0000'),
				'keyword1' => array('value'=>$data['keyword1'], 'color'=>'#FF0000'),
				'keyword2' => array('value'=>$data['keyword2'], 'color'=>'#FF0000'),
				'keyword4' => array('value'=>$data['keyword4'], 'color'=>'#FF0000'),
				'remark'   => array('value'=>$data['remark'], 'color'=>'#FF0000'),
			)
		);
		if ($template_id==1) {
			$data = $template1;	
		}
		$datajson = json_encode($data);
		$res = $this->http_curl($url, 'post', 'json', $datajson);
		return $res;
	}

	/**
	 * 获取用户openid(snsapi_base)
	 *
	 * 1.获取到code
	 * 2.获取到网页授权的access_token
	 * 3.获取用户openid
	 */
	public function getBaseInfo()
	{
		//获取code
		$appid_cs = 'wxec936f771d0f4d17';
		$redirect_uri = urlencode('http://129.28.98.199/weixin/getopenid.php');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid_cs.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		header("Location:".$url); 
	}

	public function getOpenId()
	{
		//获取网页授权access_token
		$appid_cs = 'wxec936f771d0f4d17';
		$appsecret_cs = 'a4baff1a5ed4a60ce13fef99ee4ee362';
		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid_cs.'&secret='.$appsecret_cs.'&code='.$code.'&grant_type=authorization_code';
		//获取openid
		$res = $this->http_curl($url);
		return $res['openid'];
	}

	/**
	 * 获取用户详细授权(snsapi_userinfo)
	 *
	 * 1.获取到code
	 * 2.获取到网页授权的access_token
	 * 3.获取用户userinfo
	 */
	public function getUserDetail()
	{
		//获取code
		$appid_cs = 'wxec936f771d0f4d17';
		$redirect_uri = urlencode('http://129.28.98.199/weixin/getuserinfo.php');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid_cs.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
		header("Location:".$url); 
	}

	public function getUserInfo()
	{
		//获取网页授权access_token
		$appid_cs = 'wxec936f771d0f4d17';
		$appsecret_cs = 'a4baff1a5ed4a60ce13fef99ee4ee362';
		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid_cs.'&secret='.$appsecret_cs.'&code='.$code.'&grant_type=authorization_code';
		$res = $this->http_curl($url);
		$access_token = $res['access_token'];
		$openid = $res['openid'];
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		//获取userinfo
		$res = $this->http_curl($url);
		return $res;
	}

	//获取聊天记录
	public function getMsgList($data)
	{
		header('content-type:text/html;charset=utf-8');
		$access_token = $this->getWxAccessToken();
		$url = 'https://api.weixin.qq.com/customservice/msgrecord/getmsglist?access_token='.$access_token;
		$datajson = json_encode($data);
		$res = $this->http_curl($url, 'post', 'json', $datajson);
		return $res;
	}

	//二维码接口
	public function erweima()
	{
		
	}

	//微信jssdk
	public function fx()
	{
		
	}
}
