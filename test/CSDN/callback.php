<?php
require __DIR__ . '/common.php';
$csdnOAuth = new \Yurun\OAuthLogin\CSDN\OAuth2($GLOBALS['oauth_csdn']['appid'], $GLOBALS['oauth_csdn']['appkey'], $GLOBALS['oauth_csdn']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $csdnOAuth->loginAgentUrl = 'http://test.com/test/CSDN/loginAgent.php';
var_dump(
	'access_token:', $csdnOAuth->getAccessToken(),
	'我也是access_token:', $csdnOAuth->accessToken,
	'请求返回:', $csdnOAuth->result
);
var_dump(
	'用户资料:', $csdnOAuth->getUserInfo(),
	'openid:', $csdnOAuth->openid
);