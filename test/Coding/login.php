<?php
require __DIR__ . '/common.php';
$codingOAuth = new \Yurun\OAuthLogin\Coding\OAuth2($GLOBALS['oauth_coding']['appid'], $GLOBALS['oauth_coding']['appkey'], $GLOBALS['oauth_coding']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $codingOAuth->loginAgentUrl = 'http://localhost/test/Coding/loginAgent.php';

// 可选属性
/*
// 是否在登录页显示注册
$codingOAuth->allowSignup = false;
*/
// 所有为null的可不传，这里为了演示和加注释就写了
$url = $codingOAuth->getAuthUrl(
	null,	// 回调地址，登录成功后返回该地址
	null,	// state 为空自动生成
	'user'	// scope，多个用逗号分隔
);
header('location:' . $url);