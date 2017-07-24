<?php
require __DIR__ . '/common.php';
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);
var_dump('access_token:', $qqOAuth->parseCallback($_SESSION['YURUN_QQ_STATE']), $qqOAuth->accessToken, $qqOAuth->result);
var_dump('openid:', $qqOAuth->getOpenID(), $qqOAuth->openid, $qqOAuth->result);
var_dump('info:', $qqOAuth->result);
var_dump('userinfo:', $qqOAuth->getUserInfo());