<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($GLOBALS['oauth_weibo']['appid'], $GLOBALS['oauth_weibo']['appkey'], $GLOBALS['oauth_weibo']['callbackUrl']);
var_dump('access_token:', $weiboOAuth->parseCallback($_SESSION['YURUN_WEIBO_STATE']), $weiboOAuth->accessToken, $weiboOAuth->result);
var_dump('openid:', $weiboOAuth->openid, $weiboOAuth->result);
var_dump('info:', $weiboOAuth->result);
var_dump('userinfo:', $weiboOAuth->getUserInfo());