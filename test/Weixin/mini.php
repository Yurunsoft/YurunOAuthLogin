<?php

require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($GLOBALS['oauth_weixin']['appid'], $GLOBALS['oauth_weixin']['appkey']);
$wxOAuth->getSessionKey($_GET['code']);
var_dump($wxOAuth->result);
