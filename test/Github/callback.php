<?php
require __DIR__ . '/common.php';
$githubOAuth = new \Yurun\OAuthLogin\Github\OAuth2($GLOBALS['oauth_github']['appid'], $GLOBALS['oauth_github']['appkey'], $GLOBALS['oauth_github']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $githubOAuth->loginAgentUrl = 'http://test.com/test/Github/loginAgent.php';
var_dump(
	'access_token:', $githubOAuth->getAccessToken($_SESSION['YURUN_GITHUB_STATE']),
	'我也是access_token:', $githubOAuth->accessToken,
	'请求返回:', $githubOAuth->result
);
var_dump(
	'用户资料:', $githubOAuth->getUserInfo(),
	'openid:', $githubOAuth->openid
);