<?php
require __DIR__ . '/common.php';
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2;
$wxOAuth->displayLoginAgent();