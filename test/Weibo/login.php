<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($GLOBALS['oauth_weibo']['appid'], $GLOBALS['oauth_weibo']['appkey'], $GLOBALS['oauth_weibo']['callbackUrl']);

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $weiboOAuth->getAuthUrl(
	$GLOBALS['oauth_weibo']['callbackUrl'],	// 回调地址，登录成功后返回该地址
	null,									// $state 为空自动生成
	null,									// $scope 为空默认
	null,									// $display 为空默认
	null,									// $forcelogin 为空默认
	null									// $language 为空默认
);
$_SESSION['YURUN_WEIBO_STATE'] = $weiboOAuth->state;
header('location:' . $url);