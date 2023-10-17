<?php

require __DIR__ . '/common.php';
$weworkOAuth = new \Yurun\OAuthLogin\WeWork\OAuth2($GLOBALS['oauth_wework']['appid'], $GLOBALS['oauth_wework']['appkey'], $GLOBALS['oauth_wework']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
//$weworkOAuth->loginAgentUrl = 'http://test.com/test/WeWork/loginAgent.php';

var_dump(
    'access_token:', $weworkOAuth->getAccessToken($_SESSION['YURUN_WEWORK_STATE']),
    '我也是access_token:', $weworkOAuth->accessToken,
    '请求返回:', $weworkOAuth->result
);
var_dump(
    '用户资料:', $weworkOAuth->getUserInfo(),
    'openid:', $weworkOAuth->openid
);
