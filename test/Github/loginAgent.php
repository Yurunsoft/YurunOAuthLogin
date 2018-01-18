<?php
require __DIR__ . '/common.php';
$githubOAuth = new \Yurun\OAuthLogin\Github\OAuth2;
$githubOAuth->displayLoginAgent();