<?php

require __DIR__ . '/common.php';
$gitlabOAuth = new \Yurun\OAuthLogin\GitLab\OAuth2($GLOBALS['oauth_gitlab']['appid'], $GLOBALS['oauth_gitlab']['appkey'], $GLOBALS['oauth_gitlab']['callbackUrl']);
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考./loginAgent.php写法
// $gitlabOAuth->loginAgentUrl = 'http://test.com/test/GitLab/loginAgent.php';

var_dump(
    'access_token:', $gitlabOAuth->getAccessToken($_SESSION['YURUN_GITLAB_STATE']),
    '我也是access_token:', $gitlabOAuth->accessToken,
    '请求返回:', $gitlabOAuth->result
);
var_dump(
    '用户资料:', $gitlabOAuth->getUserInfo(),
    'openid:', $gitlabOAuth->openid
);
