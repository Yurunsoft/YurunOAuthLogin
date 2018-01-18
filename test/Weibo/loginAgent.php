<?php
require __DIR__ . '/common.php';
$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2;
$weiboOAuth->displayLoginAgent();