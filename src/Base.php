<?php
namespace Yurun\OAuthLogin;

use Yurun\Until\HttpRequest;
use Yurun\OAuthLogin\ApiException;

class Base
{
	/**
	 * http请求类
	 * @var HttpRequest
	 */
	public $http;

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