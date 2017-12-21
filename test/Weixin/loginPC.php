<?php
require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($GLOBALS['oauth_weixin']['appid'], $GLOBALS['oauth_weixin']['appkey']);

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $wxOAuth->getAuthUrl(
	$GLOBALS['oauth_weixin']['callbackUrl'],	// 回调地址，登录成功后返回该地址，为null则取来源页面
	null,										// state 为空自动生成
	null										// scope 只要登录默认为空即可
);
$_SESSION['YURUN_WEIXIN_STATE'] = $wxOAuth->state;
header('location:' . $url);