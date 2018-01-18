<?php
require __DIR__ . '/common.php';
$codingOAuth = new \Yurun\OAuthLogin\Coding\OAuth2($GLOBALS['oauth_coding']['appid'], $GLOBALS['oauth_coding']['appkey'], $GLOBALS['oauth_coding']['callbackUrl']);
var_dump(
	'access_token:', $codingOAuth->getAccessToken(),
	'我也是access_token:', $codingOAuth->accessToken,
	'请求返回:', $codingOAuth->result
);
var_dump(
	'用户资料:', $codingOAuth->getUserInfo(),
	'openid:', $codingOAuth->openid
);