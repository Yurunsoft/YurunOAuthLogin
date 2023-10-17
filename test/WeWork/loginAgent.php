<?php

require __DIR__ . '/common.php';
$weworkOAuth = new \Yurun\OAuthLogin\WeWork\OAuth2();
$weworkOAuth->displayLoginAgent();
