<?php
require __DIR__ . '/common.php';
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);
var_dump(
	'access_token:', $qqOAuth->getAccessToken($_SESSION['YURUN_QQ_STATE']),
	'我也是access_token:', $qqOAuth->accessToken,
	'请求返回:', $qqOAuth->result
);
var_dump(
	'用户资料:', $qqOAuth->getUserInfo(),
	'openid:', $qqOAuth->openid
);