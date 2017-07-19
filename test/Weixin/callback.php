<?php
require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($GLOBALS['oauth_weixin']['appid'], $GLOBALS['oauth_weixin']['appkey']);
var_dump('callback_result:', $wxOAuth->parseCallback($_SESSION['YURUN_WEIXIN_STATE']));
var_dump('info:', $wxOAuth->result);
var_dump('userinfo:', $wxOAuth->getUserInfo());