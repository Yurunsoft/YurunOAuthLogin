<?php

require __DIR__ . '/common.php';
$feishuOAuth = new \Yurun\OAuthLogin\FeiShu\OAuth2($GLOBALS['oauth_feishu']['appid'], $GLOBALS['oauth_feishu']['appkey'], $GLOBALS['oauth_feishu']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $feishuOAuth->loginAgentUrl = 'http://localhost/test/FeiShu/loginAgent.php';

var_dump(
    'access_token:', $feishuOAuth->getAccessToken($_SESSION['YURUN_FEISHU_STATE']),
    '我也是access_token:', $feishuOAuth->accessToken,
    '请求返回:', $feishuOAuth->result
);
var_dump(
    '用户资料:', $feishuOAuth->getUserInfo(),
    'openid:', $feishuOAuth->openid
);
