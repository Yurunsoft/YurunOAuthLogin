<?php

require __DIR__ . '/common.php';
$baiduOAuth = new \Yurun\OAuthLogin\Baidu\OAuth2($GLOBALS['oauth_baidu']['appid'], $GLOBALS['oauth_baidu']['appkey'], $GLOBALS['oauth_baidu']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $baiduOAuth->loginAgentUrl = 'http://test.com/test/Baidu/loginAgent.php';

// 可选属性
/*
// 是否在登录页显示注册
$baiduOAuth->allowSignup = false;
*/
// 所有为null的可不传，这里为了演示和加注释就写了
$url = $baiduOAuth->getAuthUrl(
    null,	// 回调地址，登录成功后返回该地址
    null,	// state 为空自动生成
    null	// scope 只要登录默认为空即可
);
$_SESSION['YURUN_BAIDU_STATE'] = $baiduOAuth->state;
header('location:' . $url);
