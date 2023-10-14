<?php

require __DIR__ . '/common.php';
$dingtalkOAuth = new \Yurun\OAuthLogin\DingTalk\OAuth2($GLOBALS['oauth_dingtalk']['appid'], $GLOBALS['oauth_dingtalk']['appkey'], $GLOBALS['oauth_dingtalk']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $dingtalkOAuth->loginAgentUrl = 'http://test.com/test/DingTalk/loginAgent.php';

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $dingtalkOAuth->getAuthUrl(
    null,    // 回调地址，登录成功后返回该地址
    null,    // state 为空自动生成
    'openid'    // scope
);
$_SESSION['YURUN_DINGTALK_STATE'] = $dingtalkOAuth->state;
header('location:' . $url);
