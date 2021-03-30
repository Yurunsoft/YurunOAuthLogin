<?php

require dirname(__DIR__) . '/common.php';
$GLOBALS['oauth_alipay'] = [
    'appid'			       => '',
    'appPrivateKey'	 => <<<STR
这里放应用私钥内容
STR
    ,
    'callbackUrl'	 => 'http://test.com/test/Alipay/callback.php',
];
