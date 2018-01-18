<?php
require __DIR__ . '/common.php';
$codingOAuth = new \Yurun\OAuthLogin\Coding\OAuth2($GLOBALS['oauth_coding']['appid'], $GLOBALS['oauth_coding']['appkey'], $GLOBALS['oauth_coding']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $codingOAuth->loginAgentUrl = 'http://localhost/test/Coding/loginAgent.php';
var_dump(
	'access_token:', $codingOAuth->getAccessToken(),
	'我也是access_token:', $codingOAuth->accessToken,
	'请求返回:', $codingOAuth->result
);
var_dump(
	'用户资料:', $codingOAuth->getUserInfo(),
	'openid:', $codingOAuth->openid
);