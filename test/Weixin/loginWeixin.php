<?php
require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($GLOBALS['oauth_weixin']['appid'], $GLOBALS['oauth_weixin']['appkey']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $wxOAuth->loginAgentUrl = 'http://localhost/test/Weixin/loginAgent.php';

// 所有为null的可不传，这里为了演示和加注释就写了
$url = $wxOAuth->getWeixinAuthUrl(
	$GLOBALS['oauth_weixin']['callbackUrl'],	// 回调地址，登录成功后返回该地址，为null则取来源页面
	null,										// state 为空自动生成
	null										// scope 只要登录默认为空即可
);
$_SESSION['YURUN_WEIXIN_STATE'] = $wxOAuth->state;
exit($url);
header('location:' . $url);