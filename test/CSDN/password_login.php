<?php
require __DIR__ . '/common.php';
$csdnOAuth = new \Yurun\OAuthLogin\CSDN\OAuth2($GLOBALS['oauth_csdn']['appid'], $GLOBALS['oauth_csdn']['appkey'], $GLOBALS['oauth_csdn']['callbackUrl']);
var_dump(
	'access_token:', $csdnOAuth->login('username', 'password'),
	'我也是access_token:', $csdnOAuth->accessToken,
	'请求返回:', $csdnOAuth->result
);
var_dump(
	'用户资料:', $csdnOAuth->getUserInfo(),
	'openid:', $csdnOAuth->openid
);