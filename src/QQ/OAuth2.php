<?php
namespace Yurun\OAuthLogin\QQ;

use Yurun\OAuthLogin\Base;
use Yurun\OAuthLogin\ApiException;

class OAuth2 extends Base
{
	/**
	 * api接口域名
	 */
	const API_DOMAIN = 'https://graph.qq.com/';

	/**
	 * 构造方法
	 * @param string $appid 应用的唯一标识。在OAuth2.0认证过程中，appid的值即为oauth_consumer_key的值。
	 * @param string $appSecret appid对应的密钥，访问用户资源时用来验证应用的合法性。在OAuth2.0认证过程中，appSecret的值即为oauth_consumer_secret的值。
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
	 * @param string $redirectUri 成功授权后的回调地址，必须是注册appid时填写的主域名下的地址，建议设置为网站首页或网站的用户中心。为null则获取referer
	 * @param string $state 状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
	 * @param array $scope 请求用户授权时向用户显示的可进行授权的列表。可空
	 * @param string $display 仅PC网站接入时使用。用于展示的样式。不传则默认展示为PC下的样式。如果传入“mobile”，则展示为mobile端下的样式。
	 * @return string
	 */
	public function getAuthUrl($redirectUri = null, $state = null, $scope = array(), $display = null)
	{
		if(null === $state)
		{
			$this->state = md5(\uniqid('', true));
		}
		else
		{
			$this->state = $state;
		}
		return $this->getUrl('oauth2.0/authorize', array(
			'response_type'		=>	'code',
			'client_id'			=>	$this->appid,
			'redirect_uri'		=>	isset($redirectUri) ? $redirectUri : $this->callbackUrl,
			'state'				=>	$this->state,
			'scope'				=>	isset($scope[0]) ? implode(',', $scope) : null,
			'display'			=>	$display,
		));
	}

	/**
	 * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
	 * @param string $storeState 存储的正确的state
	 * @param string $state 回调接收到的state，为null则通过get参数获取
	 * @param string $code 第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
	 * @param string $redirectUri 与第一步中传入的$redirectUri保持一致。
	 * @return string
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
		return $this->getAccessToken($code, $redirectUri);
	}

	/**
	 * 第二步:获取access_token。成功返回access_token，失败抛出异常ApiException
	 * @param string $code 第一步里$redirectUri地址中传过来的code
	 * @param string $state 存储的正确的state
	 * @param string $redirectUri 与第一步中传入的$redirectUri保持一致。
	 * @return string
	 */
	public function getAccessToken($code = null, $state = null, $redirectUri = null)
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
		parse_str($this->http->get($this->getUrl('oauth2.0/token', array(
			'grant_type'	=>	'authorization_code',
			'client_id'		=>	$this->appid,
			'client_secret'	=>	$this->appSecret,
			'code'			=>	isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
			'state'			=>	$state,
			'redirect_uri'	=>	isset($redirectUri) ? $redirectUri : $this->callbackUrl,
		)))->body, $result);
		$this->result = $result;
		if(isset($this->result['code']) && 0 != $this->result['code'])
		{
			throw new ApiException($this->result['msg'], $this->result['code']);
		}
		else
		{
			return $this->accessToken = $this->result['access_token'];
		}
	}

	/**
	 * 第三步:获取OpenID
	 * @param string $accessToken
	 * @return string
	 */
	public function getOpenID($accessToken = null)
	{
		$this->result = $this->jsonp_decode($this->http->get($this->getUrl('oauth2.0/me', array(
			'access_token'	=>	null === $accessToken ? $this->accessToken : $accessToken,
		)))->body, true);
		if(isset($this->result['code']) && 0 != $this->result['code'])
		{
			throw new ApiException($this->result['msg'], $this->result['code']);
		}
		else
		{
			return $this->openid = $this->result['openid'];
		}
	}

	/**
	 * 获取登录用户在QQ空间的信息，包括昵称、头像、性别及黄钻信息（包括黄钻等级、是否年费黄钻等）。
	 * @param string $accessToken 不传则使用getAccessToken方法调用后的值
	 * @param string $openid 不传则使用getOpenID方法调用后的值
	 * @return void
	 */
	public function getUserInfo($accessToken = null, $openid = null)
	{
		$this->result = json_decode($this->http->get($this->getUrl('user/get_user_info', array(
			'access_token'			=>	null === $accessToken ? $this->accessToken : $accessToken,
			'oauth_consumer_key'	=>	$this->appid,
			'openid'				=>	null === $openid ? $this->openid : $openid,
		)))->body, true);
		if(isset($this->result['ret']) && 0 != $this->result['ret'])
		{
			throw new ApiException($this->result['msg'], $this->result['ret']);
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
		$this->result = $this->jsonp_decode($this->http->get($this->getUrl('oauth2.0/token', array(
			'grant_type'	=>	'refresh_token',
			'client_id'		=>	$this->appid,
			'client_secret'	=>	$this->appSecret,
			'refresh_token'	=>	$refreshToken,
		)))->body, true);
		return isset($this->result['code']) && 0 == $this->result['code'];
	}
}