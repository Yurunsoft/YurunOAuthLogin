<?php
namespace Yurun\OAuthLogin\Weixin;

use Yurun\OAuthLogin\Base;
use Yurun\OAuthLogin\ApiException;

class OAuth2 extends Base
{
	/**
	 * api接口域名
	 */
	const API_DOMAIN = 'https://api.weixin.qq.com/';

	/**
	 * 开放平台域名
	 */
	const OPEN_DOMAIN = 'https://open.weixin.qq.com/';

	/**
	 * 构造方法
	 * @param string $appid AppID
	 * @param string $appSecret AppSecret
	 */
	public function __construct($appid, $appSecret)
	{
		parent::__construct();
		$this->appid = $appid;
		$this->appSecret = $appSecret;
	}

	/**
	 * 获取url地址
	 * @param string $name 跟在域名后的文本
	 * @param array $params GET参数
	 * @return string
	 */
	public function getUrl($name, $params = array())
	{
		if('http' === substr($name, 0, 4))
		{
			$domain = $name;
		}
		else
		{
			$domain = static::API_DOMAIN . $name;
		}
		return $domain . (empty($params) ? '' : ('?' . \http_build_query($params)));
	}

	/**
	 * 第一步:获取登录页面跳转url
	 * @param string $redirectUri 重定向地址，为null则获取referer
	 * @param string $state 状态值，不传则自动生成，随后可以通过->state获取。用于保持请求和回调的状态，授权请求后原样带回给第三方。该参数可用于防止csrf攻击（跨站请求伪造攻击），建议第三方带上该参数，可设置为简单的随机数加session进行校验
	 * @param array $scope 应用授权作用域，拥有多个作用域用逗号（,）分隔
	 * @return string
	 */
	public function getAuthUrl($redirectUri = null, $state = null, $scope = null)
	{
		if(null === $state)
		{
			$this->state = md5(\uniqid('', true));
		}
		else
		{
			$this->state = $state;
		}
		return $this->getUrl(static::OPEN_DOMAIN . 'connect/qrconnect', array(
			'appid'				=>	$this->appid,
			'redirect_uri'		=>	isset($redirectUri) ? $redirectUri : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
			'response_type'		=>	'code',
			'state'				=>	$this->state,
			'scope'				=>	null === $scope ? 'snsapi_login' : null,
		));
	}

	/**
	 * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
	 * @param string $storeState 存储的正确的state
	 * @param string $state 回调接收到的state，为null则通过get参数获取
	 * @param string $code 第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
	 * @return bool
	 */
	public function parseCallback($storeState, $state = null, $code = null)
	{
		if(null === $state)
		{
			if(isset($_GET['state']))
			{
				$state = $_GET['state'];
			}
			else
			{
				$state = '';
			}
		}
		if($storeState !== $state)
		{
			throw new \InvalidArgumentException('state验证失败');
		}
		$this->result = json_decode($this->http->get($this->getUrl('sns/oauth2/access_token', array(
			'appid'			=>	$this->appid,
			'secret'		=>	$this->appSecret,
			'code'			=>	isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
			'grant_type'	=>	'authorization_code',
		)))->body, true);
		if(isset($this->result['errcode']) && 0 != $this->result['errcode'])
		{
			throw new ApiException($this->result['errmsg'], $this->result['errcode']);
		}
		else
		{
			$this->accessToken = $this->result['access_token'];
			$this->openid = $this->result['openid'];
			return true;
		}
	}

	/**
	 * 检验授权凭证（access_token）是否有效
	 * @param string $accessToken 不传则使用parseCallback方法调用后的值
	 * @param string $openid 普通用户标识，对该公众帐号唯一，不传则使用parseCallback方法调用后的值
	 * @return bool
	 */
	public function validateAccessToken($accessToken = null, $openid = null)
	{
		$this->result = json_decode($this->http->get($this->getUrl('sns/auth', array(
			'access_token'	=>	$accessToken,
			'openid'		=>	$openid,
		)))->body, true);
		return isset($this->result['errcode']) && 0 == $this->result['errcode'];
	}

	/**
	 * 获取用户个人信息（UnionID机制）
	 * @param string $accessToken 不传则使用parseCallback方法调用后的值
	 * @param string $openid 普通用户标识，对该公众帐号唯一，不传则使用parseCallback方法调用后的值
	 * @return void
	 */
	public function getUserInfo($accessToken = null, $openid = null)
	{
		$this->result = json_decode($this->http->get($this->getUrl('sns/userinfo', array(
			'access_token'	=>	null === $accessToken ? $this->accessToken : $accessToken,
			'openid'		=>	null === $openid ? $this->openid : $openid,
		)))->body, true);
		if(isset($this->result['errcode']) && 0 != $this->result['errcode'])
		{
			throw new ApiException($this->result['errmsg'], $this->result['errcode']);
		}
		else
		{
			return $this->result;
		}
	}

	/**
	 * 刷新AccessToken续期
	 * @param string $refreshToken
	 * @return bool
	 */
	public function refreshToken($refreshToken)
	{
		$this->result = json_decode($this->http->get($this->getUrl('sns/oauth2/refresh_token', array(
			'appid'			=>	$this->appid,
			'grant_type'	=>	'refresh_token',
			'refresh_token'	=>	$refreshToken,
		)))->body, true);
		return isset($this->result['errcode']) && 0 == $this->result['errcode'];
	}
}