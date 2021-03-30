<?php
/**
 * QQ 小程序解密数据演示.
 */
require __DIR__ . '/common.php';
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey']);
// 获取 code
$code = $_POST['code'];
// code 换 sessionKey
$sessionKey = $qqOAuth->getSessionKey($code);
// 解密
$data = $qqOAuth->descryptData($_POST['encrypted_data'], $_POST['iv'], $sessionKey);
var_dump($data);
