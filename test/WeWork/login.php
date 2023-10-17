<?php

require __DIR__ . '/common.php';
$weworkOAuth = new \Yurun\OAuthLogin\WeWork\OAuth2($GLOBALS['oauth_wework']['appid'], $GLOBALS['oauth_wework']['appkey'], $GLOBALS['oauth_wework']['callbackUrl'], $GLOBALS['oauth_wework']['agentid']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
//$weworkOAuth->loginAgentUrl = 'http://test.com/test/WeWork/loginAgent.php';

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $weworkOAuth->getAuthUrl(
    null,    // 回调地址，登录成功后返回该地址
    null,    // state 为空自动生成
    'snsapi_privateinfo'    // scope
);
$_SESSION['YURUN_WEWORK_STATE'] = $weworkOAuth->state;
header('location:' . $url);
