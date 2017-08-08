<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($GLOBALS['oauth_weibo']['appid'], $GLOBALS['oauth_weibo']['appkey'], $GLOBALS['oauth_weibo']['callbackUrl']);
var_dump(
	'access_token:', $weiboOAuth->getAccessToken($_SESSION['YURUN_WEIBO_STATE']),
	'我也是access_token:', $weiboOAuth->accessToken,
	'请求返回:', $weiboOAuth->result
);
var_dump(
	'用户资料:', $weiboOAuth->getUserInfo(),
	'openid:', $weiboOAuth->openid
);