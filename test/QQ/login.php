<?php
require __DIR__ . '/common.php';
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);

// 可选属性
/*
// display 电脑为空，手机为mobile
$qqOAuth->display = null;
*/
// 所有为null的可不传，这里为了演示和加注释就写了
$url = $qqOAuth->getAuthUrl(
	null,	// 回调地址，登录成功后返回该地址
	null,	// state 为空自动生成
	null	// scope 只要登录默认为空即可
);
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
header('location:' . $url);