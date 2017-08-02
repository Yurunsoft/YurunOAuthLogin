<?php
namespace Yurun\OAuthLogin;

use Yurun\Until\HttpRequest;

class Base
{
	/**
	 * http请求类
	 * @var HttpRequest
	 */
	public $http;

	/**
	 * 应用的唯一标识。
	 * @var string
	 */
	public $appid;

	/**
	 * appid对应的密钥
	 * @var string
	 */
	public $appSecret;

	/**
	 * 登录回调地址
	 * @var string
	 */
	public $callbackUrl;

	/**
	 * state值，调用getAuthUrl方法后可以获取到
	 * @var string
	 */
	public $state;

	/**
	 * 接口调用结果
	 * @var array
	 */
	public $result;

	/**
	 * AccessToken，调用相应方法后可以获取到
	 * @var string
	 */
	public $accessToken;

	/**
	 * open，调用相应方法后可以获取到
	 * @var string
	 */
	public $openid;

	public function __construct()
	{
		$this->http = new HttpRequest;
	}

	/**
	 * 把jsonp转为php数组
	 * @param string $jsonp
	 * @param boolean $assoc
	 * @return array
	 */
	public function jsonp_decode($jsonp, $assoc = false)
	{
		$jsonp = trim($jsonp);
		if(isset($jsonp[0]) && $jsonp[0] !== '[' && $jsonp[0] !== '{') {
			$begin = strpos($jsonp, '(');
			if(false !== $begin)
			{
				$end = strrpos($jsonp, ')');
				if(false !== $end)
				{
					$jsonp = substr($jsonp, $begin + 1, $end - $begin - 1);
				}
			}
		}
		return json_decode($jsonp, $assoc);
	}
}