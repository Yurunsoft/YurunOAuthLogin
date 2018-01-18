<?php
require __DIR__ . '/common.php';
$oschinaOAuth = new \Yurun\OAuthLogin\OSChina\OAuth2($GLOBALS['oauth_oschina']['appid'], $GLOBALS['oauth_oschina']['appkey'], $GLOBALS['oauth_oschina']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $oschinaOAuth->loginAgentUrl = 'http://yurun.com/test/OSChina/loginAgent.php';
var_dump(
	'access_token:', $oschinaOAuth->getAccessToken($_SESSION['YURUN_OSCHINA_STATE']),
	'我也是access_token:', $oschinaOAuth->accessToken,
	'请求返回:', $oschinaOAuth->result
);
var_dump(
	'用户资料:', $oschinaOAuth->getUserInfo(),
	'openid:', $oschinaOAuth->openid
);