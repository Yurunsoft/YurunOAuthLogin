<?php
require __DIR__ . '/common.php';
$giteeOAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($GLOBALS['oauth_gitee']['appid'], $GLOBALS['oauth_gitee']['appkey'], $GLOBALS['oauth_gitee']['callbackUrl']);
var_dump(
	'access_token:', $giteeOAuth->login('username', 'password'),
	'我也是access_token:', $giteeOAuth->accessToken,
	'请求返回:', $giteeOAuth->result
);
var_dump(
	'用户资料:', $giteeOAuth->getUserInfo(),
	'openid:', $giteeOAuth->openid
);