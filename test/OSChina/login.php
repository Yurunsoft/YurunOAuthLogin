<?php
require __DIR__ . '/common.php';
$oschinaOAuth = new \Yurun\OAuthLogin\OSChina\OAuth2($GLOBALS['oauth_oschina']['appid'], $GLOBALS['oauth_oschina']['appkey'], $GLOBALS['oauth_oschina']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $oschinaOAuth->loginAgentUrl = 'http://test.com/test/OSChina/loginAgent.php';

// 可选属性
/*
// 是否在登录页显示注册
$oschinaOAuth->allowSignup = false;
*/
// 所有为null的可不传，这里为了演示和加注释就写了
$url = $oschinaOAuth->getAuthUrl(
	null,	// 回调地址，登录成功后返回该地址
	null,	// state 为空自动生成
	'user'	// scope，多个用逗号分隔
);
$_SESSION['YURUN_OSCHINA_STATE'] = $oschinaOAuth->state;
header('location:' . $url);