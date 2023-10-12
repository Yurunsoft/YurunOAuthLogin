<?php

require __DIR__ . '/common.php';
$feishuOAuth = new \Yurun\OAuthLogin\FeiShu\OAuth2($GLOBALS['oauth_feishu']['appid'], $GLOBALS['oauth_feishu']['appkey'], $GLOBALS['oauth_feishu']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $feishuOAuth->loginAgentUrl = 'http://test.com/test/FeiShu/loginAgent.php';

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $feishuOAuth->getAuthUrl(
    null,    // 回调地址，登录成功后返回该地址
    null,    // state 为空自动生成
    'contact:user.base:readonly'    // scope，多个用逗号分隔
);
$_SESSION['YURUN_FEISHU_STATE'] = $feishuOAuth->state;
header('location:' . $url);
