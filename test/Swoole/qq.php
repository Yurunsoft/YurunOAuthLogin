<?php
/**
 * Swoole 协程 Demo
 * 请先安装 Swoole 扩展
 * 运行方式：php test/Swoole/qq.php
 * 
 * 请勿直接将本文件用于生产环境，仅作为演示用
 * Swoole 暂时仅有 QQ 登录演示，但其实用法和传统方式基本一致
 */
require dirname(__DIR__) . '/common.php';

use Yurun\Util\YurunHttp;

$GLOBALS['oauth_qq'] = array(
	'appid'			=>	'',
	'appkey'		=>	'',
	'callbackUrl'	=>	'http://test.com/test/QQ/callback.php',
	'loginAgentUrl'	=>	'',
);

// 设置 Http 请求处理器为 Swoole
YurunHttp::setDefaultHandler('Yurun\Util\YurunHttp\Handler\Swoole');

$server = new swoole_http_server('0.0.0.0', 80);
$server->on('request', function ($request, $response) {
	switch($request->server['request_uri'])
	{
		case '/login':
			login($request, $response);
			break;
		case '/callback':
			callback($request, $response);
			break;
		default:
			$response->end('404');
			break;
	}
});
$server->start();

function login($request, $response)
{
	$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);
	// $qqOAuth->loginAgentUrl = $GLOBALS['oauth_qq']['loginAgentUrl'];
	$url = $qqOAuth->getAuthUrl(
		// null,	// 回调地址，登录成功后返回该地址
		// null,	// state 为空自动生成
		// null	// scope 只要登录默认为空即可
	);
	file_put_contents(__DIR__ . '/state.txt', $qqOAuth->state);
	return $response->redirect($url);
}

function callback($request, $response)
{
	$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($GLOBALS['oauth_qq']['appid'], $GLOBALS['oauth_qq']['appkey'], $GLOBALS['oauth_qq']['callbackUrl']);
	$state = file_get_contents(__DIR__ . '/state.txt');
	echo 'state:', $state, PHP_EOL;
	$response->end(json_encode([
		// swoole 协程模式下的用法，就是参数都是要传的，以往不传可以自动从 $_GET /$_SERVER 等超全局变量中获取，现在必须手动传入
		'access_token:' => $qqOAuth->getAccessToken($state, $request->get['code'], $request->get['state']),
		'我也是access_token:' => $qqOAuth->accessToken,
		'请求返回:' => $qqOAuth->result,
		'用户资料:' => $qqOAuth->getUserInfo(),
		'openid:' => $qqOAuth->openid
	]));
}