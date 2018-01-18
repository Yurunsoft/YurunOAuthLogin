<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($GLOBALS['oauth_weibo']['appid'], $GLOBALS['oauth_weibo']['appkey'], $GLOBALS['oauth_weibo']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $weiboOAuth->loginAgentUrl = 'http://test.com/test/Weibo/loginAgent.php';
var_dump(
	'access_token:', $weiboOAuth->getAccessToken($_SESSION['YURUN_WEIBO_STATE']),
	'我也是access_token:', $weiboOAuth->accessToken,
	'请求返回:', $weiboOAuth->result
);
var_dump(
	'用户资料:', $weiboOAuth->getUserInfo(),
	'openid:', $weiboOAuth->openid
);