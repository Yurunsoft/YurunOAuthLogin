<?php
require __DIR__ . '/common.php';
$giteeOAuth = new \Yurun\OAuthLogin\Gitee\OAuth2;
$giteeOAuth->displayLoginAgent();