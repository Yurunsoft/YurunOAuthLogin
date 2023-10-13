<?php

require __DIR__ . '/common.php';
$dingtalkOAuth = new \Yurun\OAuthLogin\DingTalk\OAuth2($GLOBALS['oauth_dingtalk']['appid'], $GLOBALS['oauth_dingtalk']['appkey'], $GLOBALS['oauth_dingtalk']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $dingtalkOAuth->loginAgentUrl = 'http://test.com/test/DingTalk/loginAgent.php';

var_dump(
    'access_token:', $dingtalkOAuth->getAccessToken($_SESSION['YURUN_DINGTALK_STATE']),
    '我也是access_token:', $dingtalkOAuth->accessToken,
    '请求返回:', $dingtalkOAuth->result
);
var_dump(
    '用户资料:', $dingtalkOAuth->getUserInfo(),
    'openid:', $dingtalkOAuth->openid
);
