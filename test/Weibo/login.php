<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($GLOBALS['oauth_weibo']['appid'], $GLOBALS['oauth_weibo']['appkey'], $GLOBALS['oauth_weibo']['callbackUrl']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $weiboOAuth->loginAgentUrl = 'http://test.com/test/Weibo/loginAgent.php';

// 可选属性
/*
// 授权页面的终端类型，取值见微博文档。http://open.weibo.com/wiki/Oauth2/authorize
$qqOAuth->display = null;
// 是否强制用户重新登录，true：是，false：否。默认false。
$qqOAuth->forcelogin = false;
// 授权页语言，缺省为中文简体版，en为英文版。
$qqOAuth->language = null;
*/
// 所有为null的可不传，这里为了演示和加注释就写了
$url = $weiboOAuth->getAuthUrl(
	null,	// 回调地址，登录成功后返回该地址
	null,	// state 为空自动生成
	null	// scope 只要登录默认为空即可
);
$_SESSION['YURUN_WEIBO_STATE'] = $weiboOAuth->state;
header('location:' . $url);