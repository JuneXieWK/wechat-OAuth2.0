<?php
/**
 * 获取用户openid
 */
error_reporting(E_ALL & ~E_NOTICE);

include './oauth2.php';
$code=$_GET['code'];//code 微信接口参数(必须)
$state=$_GET['state'];//state微信接口参数(不需传参则不用)；若传参可考虑规则： 'act'.'arg1'.'add'.'arg2'


$APPID='wx922bb25878a01bea';
$SECRET='124b06ab6e1bb82b2d46b24e56fee1f3';
$REDIRECT_URL='http://wkly.com/dev/react/wechat-OAuth2.0/demo/testopenid.php';//当前页面地址

$oauth2=new oauth2();
$oauth2->init($APPID, $SECRET,$REDIRECT_URL);
if(empty($code)){		
	$oauth2->get_code($state);//获取code，会重定向到当前页。若需传参，使用$state变量传参。
}
$openid=$oauth2->get_openid();//获取openid

echo '</br>welcome test!';
echo '</br>code: '.$code;
echo '</br>openid: '.$openid;
?>