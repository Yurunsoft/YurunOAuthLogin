<?php

require __DIR__ . '/common.php';
$weworkOAuth = new \Yurun\OAuthLogin\WeWork\OAuth2($GLOBALS['oauth_wework']['appid'], $GLOBALS['oauth_wework']['appkey'], $GLOBALS['oauth_wework']['callbackUrl'], $GLOBALS['oauth_wework']['agentid']);

// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
//$weworkOAuth->loginAgentUrl = 'http://test.com/test/WeWork/loginAgent.php';

$url = $weworkOAuth->getWebAuthUrl();
$_SESSION['YURUN_WEWORK_STATE'] = $weworkOAuth->state;
header('location:' . $url);
