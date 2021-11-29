<?php

require __DIR__ . '/common.php';
$gitlabOAuth = new \Yurun\OAuthLogin\GitLab\OAuth2();
$gitlabOAuth->displayLoginAgent();
