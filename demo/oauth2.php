<?php
header("Content-Type: text/html;charset=utf-8");

class oauth2{
	public $REDIRECT_URL="";
 	public $APPID="";
 	public $SECRET="";
 	
 	public $Code="";
 	public $State="";
 	public $Access_token="";
 	
	 public $Openid="";
 	
 	function __construct(){		
 		//默认使用的appid
 		$this->APPID='';
 		$this->SECRET='';		
 	} 	
    
 	/**
 	 * 初始化参数。(包括微信接口参数$code、$state)
 	 * @param string $APPID
 	 * @param string $SECRET
 	 * @param string $REDIRECT_URL
 	 */
 	function init($APPID,$SECRET,$REDIRECT_URL='http://www.weclouds.cn/api/wechat/test.php'){
 		$this->REDIRECT_URL=$REDIRECT_URL;
 		$this->APPID=$APPID;
 		$this->SECRET=$SECRET;
 		
 		$this->Code=$_GET['code'];//code
 		$this->State=$_GET['state'];//state参数

 	}
 	
 	/**
 	 * 获取Code
 	 * (传递state参数)
 	 */
 	function get_code($state='1'){		
 		$APPID=$this->APPID;
 		$redirect_uri=$this->REDIRECT_URL;
 		$url_get_code="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APPID&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=$state#wechat_redirect";
 		header("Location: $url_get_code");//重定向请求微信用户信息
 	}
 	/**
 	 * 获取用户openid
 	 * @param string $redirect_uri
 	 * @param string $state 传参
 	 */
 	function get_openid(){
 		$APPID=$this->APPID;
 		$SECRET=$this->SECRET;
 		$code=$this->Code;
 		
 		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APPID&secret=$SECRET&code=$code&grant_type=authorization_code";
		$content=file_get_contents($url);
		$o=json_decode($content,true);
		$this->Openid=$o['openid'];
		return $o['openid'];
 	}
 	
 	/**
 	 * 授权获取code
 	 */
 	function get_code_by_authorize($state){
 		$APPID=$this->APPID;
 		$redirect_uri=$this->REDIRECT_URL;
 		$url_get_code="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$APPID&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
 		header("Location: $url_get_code");//重定向请求微信用户信息		
 	}
 	
 	/**
 	 * 授权获取用户信息
 	 */
 	function get_userinfo_by_authorize(){
 		$APPID=$this->APPID;
 		$SECRET=$this->SECRET;
 		$code=$this->Code;
 			
 		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APPID&secret=$SECRET&code=$code&grant_type=authorization_code";
 		$content=file_get_contents($url);
 		$o=json_decode($content,true);
 		$openid=$o['openid'];
		$access_token=$o['access_token'];
 		
 		$url2="https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
		$content2=file_get_contents($url2);
		$Temp = $content2;
 		$o2=json_decode($content2,true);//微信获取用户信息
 		
 		//处理昵称里的特殊字符
 		$str_nickname=substr($content2,strpos($content2,",")+1);
 		$str_nickname=substr($str_nickname,12,strpos($str_nickname,",")-13);
 		
 		$data=array('nickname'=>'','heading'=>'');
 		$data['nickname']=$str_nickname;
		$data['headimgurl']=$o2['headimgurl'];
		$data['dataRaw'] = $Temp;
 		
 		return $data;
 		 		
	 }
	 private function getAccessToken() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(file_get_contents("access_token.json"));
		if ($data->expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->APPID."&secret=".$this->SECRET;
			$res = json_decode($this->http_request($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$data->expire_time = time() + 7000;
				$data->access_token = $access_token;
				$fp = fopen("access_token.json", "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$access_token = $data->access_token;
		}
		return $access_token;
	}
 	function send_template_message($data){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$res = $this->http_request($url, $data);
		return json_decode($res, true);
	}
	function http_request($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
 	
 }
?>