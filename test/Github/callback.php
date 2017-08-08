<?php
require __DIR__ . '/common.php';
$githubOAuth = new \Yurun\OAuthLogin\Github\OAuth2($GLOBALS['oauth_github']['appid'], $GLOBALS['oauth_github']['appkey'], $GLOBALS['oauth_github']['callbackUrl']);
var_dump(
	'access_token:', $githubOAuth->getAccessToken($_SESSION['YURUN_GITHUB_STATE']),
	'我也是access_token:', $githubOAuth->accessToken,
	'请求返回:', $githubOAuth->result
);
var_dump(
	'用户资料:', $githubOAuth->getUserInfo(),
	'openid:', $githubOAuth->openid
);