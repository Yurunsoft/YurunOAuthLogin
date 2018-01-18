<?php
require __DIR__ . '/common.php';
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $qqOAuth->loginAgentUrl = 'http://localhost/test/QQ/loginAgent.php';
var_dump(
	'access_token:', $qqOAuth->getAccessToken($_SESSION['YURUN_QQ_STATE']),
	'我也是access_token:', $qqOAuth->accessToken,
	'请求返回:', $qqOAuth->result
);
var_dump(
	'用户资料:', $qqOAuth->getUserInfo(),
	'openid:', $qqOAuth->openid
);