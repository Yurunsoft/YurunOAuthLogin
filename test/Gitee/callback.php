<?php
require __DIR__ . '/common.php';
$giteeOAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($GLOBALS['oauth_gitee']['appid'], $GLOBALS['oauth_gitee']['appkey'], $GLOBALS['oauth_gitee']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $giteeOAuth->loginAgentUrl = 'http://test.com/test/Gitee/loginAgent.php';
var_dump(
	'access_token:', $giteeOAuth->getAccessToken($_SESSION['YURUN_GITEE_STATE']),
	'我也是access_token:', $giteeOAuth->accessToken,
	'请求返回:', $giteeOAuth->result
);
var_dump(
	'用户资料:', $giteeOAuth->getUserInfo(),
	'openid:', $giteeOAuth->openid
);