<?php

require __DIR__ . '/common.php';
$alipayOAuth = new \Yurun\OAuthLogin\Alipay\OAuth2($GLOBALS['oauth_alipay']['appid'], $GLOBALS['oauth_alipay']['appkey'], $GLOBALS['oauth_alipay']['callbackUrl']);
$alipayOAuth->appPrivateKey = $GLOBALS['oauth_alipay']['appPrivateKey'];
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $alipayOAuth->loginAgentUrl = 'http://test.com/test/Alipay/loginAgent.php';
var_dump(
    'access_token:', $alipayOAuth->getAccessToken($_SESSION['YURUN_ALIPAY_STATE']),
    '我也是access_token:', $alipayOAuth->accessToken,
    '请求返回:', $alipayOAuth->result
);
var_dump(
    '用户资料:', $alipayOAuth->getUserInfo(),
    'openid:', $alipayOAuth->openid
);
