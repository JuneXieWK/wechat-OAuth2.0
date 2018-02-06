<?php
header("Content-Type: text/html;charset=utf-8");
/**
 * 获取用户openid
 */
error_reporting(E_ALL & ~E_NOTICE);

include './oauth2.php';
$code=$_GET['code'];//code 微信接口参数(必须)
$state=$_GET['state'];//state微信接口参数(不需传参则不用)；若传参可考虑规则： 'act'.'arg1'.'add'.'arg2'


$APPID='wx922bb25878a01bea';
$SECRET='124b06ab6e1bb82b2d46b24e56fee1f3';
$templateMsgID = '1zFGoGtcuC9s81ph8QWClXhBkGkjCP2qZVojVfN9QCs';
$templateMsgUrl = 'http://www.baidu.com';
$REDIRECT_URL='http://www.wkly.com/dev/react/wechat-OAuth2.0/demo/testopenid.php';//当前页面地址

$oauth2=new oauth2();
$oauth2->init($APPID, $SECRET,$REDIRECT_URL);
if(empty($code)){		
	$oauth2->get_code($state);//获取code，会重定向到当前页。若需传参，使用$state变量传参。
}else{
	$openid=$oauth2->get_openid();//获取openid
	echo '</br>welcome test!';
	echo '</br>code: '.$code;
	echo '</br>openid: '.$openid;
	$template = array(
		'touser'=>$openid,
		'template_id'=> $templateMsgID,
		'url'=>$templateMsgUrl,
		'topcolor'=>"#7B68EE",
		'data'=>array(
			'first' => array('value'=>urlencode("你好"), 'color'=>'#743A3A'),
			'keyword1'=>array('value'=>urlencode("XXXXX"), 'color'=>'#FF0000'),
			'keyword2'=>array('value'=>date('Y-m-d H:i:s'), 'color'=>'#FF0000'),
			'keyword3'=>array('value'=>urlencode("XXXXX"), 'color'=>'#FF0000'),
			'remark'=>array('value'=>urlencode("XXXXX"), 'color'=>'#FF0000'),
		)
	);
	echo '</br>';
	var_dump($oauth2->send_template_message(urldecode(json_encode($template))));
}

?>