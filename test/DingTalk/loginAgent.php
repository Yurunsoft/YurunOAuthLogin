<?php

require __DIR__ . '/common.php';
$dingtalkOAuth = new \Yurun\OAuthLogin\DingTalk\OAuth2();
$dingtalkOAuth->displayLoginAgent();
