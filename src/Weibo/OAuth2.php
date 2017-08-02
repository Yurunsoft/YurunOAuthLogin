<?php
namespace Yurun\OAuthLogin\Weibo;

use Yurun\OAuthLogin\Base;
use Yurun\OAuthLogin\ApiException;

class OAuth2 extends Base
{
	/**
	 * api域名
	 */
	const API_DOMAIN = 'https://api.weibo.com/';

	/**
	 * 构造方法
	 * @param string $appid AppID
	 * @param string $appSecret AppSecret
	 * @param string $callbackUrl 登录回调地址
	 */
	public function __construct($appid, $appSecret, $callbackUrl)
	{
		parent::__construct();
		$this->appid = $appid;
		$this->appSecret = $appSecret;
		$this->callbackUrl = $callbackUrl;
	}

	/**
	 * 获取url地址
	 * @param string $name 跟在域名后的文本
	 * @param array $params GET参数
	 * @return string
	 */
	public function getUrl($name, $params = array())
	{
		return static::API_DOMAIN . $name . (empty($params) ? '' : ('?' . \http_build_query($params)));
	}

	/**
	 * 第一步:获取登录页面跳转url
	 * @param string $redirectUri 重定向地址，为null则获取referer
	 * @param string $state 状态值，不传则自动生成，随后可以通过->state获取。用于保持请求和回调的状态，授权请求后原样带回给第三方。该参数可用于防止csrf攻击（跨站请求伪造攻击），建议第三方带上该参数，可设置为简单的随机数加session进行校验
	 * @param array $scope 应用授权作用域，拥有多个作用域用逗号（,）分隔
	 * @param array $display 授权页面的终端类型，取值见微博文档。http://open.weibo.com/wiki/Oauth2/authorize
	 * @param array $forcelogin 是否强制用户重新登录，true：是，false：否。默认false。
	 * @param array $language 授权页语言，缺省为中文简体版，en为英文版。
	 * @return string
	 */
	public function getAuthUrl($redirectUri = null, $state = null, $scope = null, $display = null, $forcelogin = null, $language = null)
	{
		if(null === $state)
		{
			$this->state = md5(\uniqid('', true));
		}
		else
		{
			$this->state = $state;
		}
		return $this->getUrl('oauth2/authorize', array(
			'client_id'			=>	$this->appid,
			'redirect_uri'		=>	isset($redirectUri) ? $redirectUri : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
			'scope'				=>	$scope,
			'state'				=>	$this->state,
			'display'			=>	$display,
			'forcelogin'		=>	$forcelogin,
			'language'			=>	$language,
		));
	}

	/**
	 * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
	 * @param string $storeState 存储的正确的state
	 * @param string $state 回调接收到的state，为null则通过get参数获取
	 * @param string $code 第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
	 * @param string $redirectUri 与第一步中传入的$redirectUri保持一致。
	 * @return bool
	 */
	public function parseCallback($storeState, $state = null, $code = null, $redirectUri = null)
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
		$this->result = json_decode($this->http->post($this->getUrl('oauth2/access_token'), array(
			'client_id'		=>	$this->appid,
			'client_secret'	=>	$this->appSecret,
			'grant_type'	=>	'authorization_code',
			'code'			=>	isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
			'redirect_uri'	=>	isset($redirectUri) ? $redirectUri : $this->callbackUrl,
		))->body, true);
		if(isset($this->result['error_code']))
		{
			throw new ApiException($this->result['error'], $this->result['error_code']);
		}
		else
		{
			$this->accessToken = $this->result['access_token'];
			$this->openid = $this->result['uid'];
			return true;
		}
	}

	/**
	 * 检验授权凭证（access_token）是否有效
	 * @param string $accessToken 不传则使用parseCallback方法调用后的值
	 * @return bool
	 */
	public function validateAccessToken($accessToken = null)
	{
		$this->result = json_decode($this->http->post($this->getUrl('oauth2/get_token_info'), array(
			'access_token'	=>	null === $accessToken ? $this->accessToken : $accessToken,
		))->body, true);
		if(isset($this->result['error_code']))
		{
			throw new ApiException($this->result['error'], $this->result['error_code']);
		}
		else
		{
			return $this->result['expire_in'] > 0;
		}
	}

	/**
	 * 获取用户个人信息（UnionID机制）
	 * @param string $accessToken 不传则使用parseCallback方法调用后的值
	 * @param string $uid 不传则使用parseCallback方法调用后的openid值
	 * @param string $screenName 不传则为null
	 * @return void
	 */
	public function getUserInfo($accessToken = null, $uid = null, $screenName = null)
	{
		$this->result = json_decode($this->http->get($this->getUrl('2/users/show.json', array(
			'access_token'	=>	null === $accessToken ? $this->accessToken : $accessToken,
			'uid'			=>	null === $uid ? $this->openid : $uid,
			'screenName'	=>	$screenName,
		)))->body, true);
		if(isset($this->result['error_code']))
		{
			throw new ApiException($this->result['error'], $this->result['error_code']);
		}
		else
		{
			return $this->result;
		}
	}

	/**
	 * 刷新AccessToken续期，微博不支持
	 * @param string $refreshToken
	 * @return bool
	 */
	public function refreshToken($refreshToken)
	{
		return false;
	}
}