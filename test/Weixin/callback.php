<?php
require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($GLOBALS['oauth_weixin']['appid'], $GLOBALS['oauth_weixin']['appkey']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $wxOAuth->loginAgentUrl = 'http://localhost/test/Weixin/loginAgent.php';
// 可选属性
/*
// openid值从哪个字段取；OPEN_ID-openid；UNION_ID-unionid；UNION_ID_FIRST-优先使用unionid，如果没有则使用openid
$wxOAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::OPEN_ID;
*/
var_dump(
	'access_token:', $wxOAuth->getAccessToken($_SESSION['YURUN_WEIXIN_STATE']),
	'我也是access_token:', $wxOAuth->accessToken,
	'请求返回:', $wxOAuth->result
);
var_dump(
	'用户资料:', $wxOAuth->getUserInfo(),
	'openid:', $wxOAuth->openid
);