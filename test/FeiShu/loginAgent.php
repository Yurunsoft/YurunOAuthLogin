<?php

require __DIR__ . '/common.php';
$feishuOAuth = new \Yurun\OAuthLogin\FeiShu\OAuth2();
$feishuOAuth->displayLoginAgent();
