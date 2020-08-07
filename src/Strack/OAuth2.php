<?php

namespace Yurun\OAuthLogin\Strack;

use Yurun\OAuthLogin\Base;
use Yurun\OAuthLogin\ApiException;

class OAuth2 extends Base
{
    /**
     * api域名
     */
    protected $baseUrl = '';

    /**
     * 因为链接不确定所以需要手动设置
     * @param $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url . '/';
    }

    /**
     * 获取url地址
     * @param string $name 跟在域名后的文本
     * @param array $params GET参数
     * @return string
     */
    public function getUrl($name, $params = array())
    {
        return $this->baseUrl . $name . (empty($params) ? '' : ('?' . $this->http_build_query($params)));
    }

    /**
     * 第一步:获取登录页面跳转url
     * @param string $callbackUrl 登录回调地址
     * @param string $state 状态值，不传则自动生成，随后可以通过->state获取。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。一般为每个用户登录时随机生成state存在session中，登录回调中判断state是否和session中相同
     * @param array $scope 无用
     * @return string
     */
    public function getAuthUrl($callbackUrl = null, $state = null, $scope = null)
    {
        $option = array(
            'app_key' => $this->appid,
            'redirect_uri' => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'response_type' => 'code',
            'state' => $this->getState($state),
        );
        if (null === $this->loginAgentUrl) {
            return $this->getUrl('oauth/authorize', $option);
        } else {
            return $this->loginAgentUrl . '?' . $this->http_build_query($option);
        }
    }

    /**
     * 第二步:处理回调并获取access_token。与getAccessToken不同的是会验证state值是否匹配，防止csrf攻击。
     * @param string $storeState 存储的正确的state
     * @param string $code 第一步里$redirectUri地址中传过来的code，为null则通过get参数获取
     * @param string $state 回调接收到的state，为null则通过get参数获取
     * @return string
     * @throws ApiException
     */
    protected function __getAccessToken($storeState, $code = null, $state = null)
    {
        $response = $this->http->post($this->getUrl('oauth/getAuthorizeToken'), array(
            'grant_type' => 'authorization_code',
            'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'app_key' => $this->appid,
            'redirect_uri' => $this->getRedirectUri(),
            'app_secret' => $this->appSecret,
            'device_unique_code' => isset($_GET['device_unique_code']) ? $_GET['device_unique_code'] : '' // 传入设备唯一值
        ));

        $this->result = $response->json(true);

        if ((int)$this->result['code'] === 0) {
            return $this->accessToken = $this->result['data']['access_token'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 获取用户其他系统同一设备登录状态
     * @param $deviceUniqueCode
     * @param null $state
     * @return mixed
     * @throws ApiException
     */
    public function getUserSsoStatus($deviceUniqueCode, $state = null)
    {
        $response = $this->http->get($this->getUrl('oauth/getUserSsoStatus', array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'state' => $this->getState($state),
            'device_unique_code' => $deviceUniqueCode // 传入设备唯一值
        )));
        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 注销当前设备用户登录状态
     * @param $deviceUniqueCode
     * @param null $state
     * @return mixed
     * @throws ApiException
     */
    public function cancelUserSsoStatus($deviceUniqueCode, $state = null)
    {
        $response = $this->http->get($this->getUrl('oauth/cancelUserSsoStatus', array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'state' => $this->getState($state),
            'device_unique_code' => $deviceUniqueCode // 传入设备唯一值
        )));
        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 获取用户资料
     * @param null $accessToken
     * @return array
     * @throws ApiException
     */
    public function getUserInfo($accessToken = null)
    {
        $response = $this->http->get($this->getUrl('oauth/getUserInfo', array(
            'access_token' => null === $accessToken ? $this->accessToken : $accessToken,
        )));
        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            $this->openid = $this->result['data']['id'];
            return $this->result;
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 同步注册用户
     * @param $data
     * @return array
     * @throws ApiException
     */
    public function syncRegister($data)
    {
        $response = $this->http->get($this->getUrl('oauth/syncRegister', array(
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => !empty($data['password']) ? $data['password'] : '',
            'sex' => !empty($data['sex']) ? $data['sex'] : 'male',
        )));

        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            return $this->result;
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 获取用户资料通过令牌
     * @param null $code
     * @return array
     * @throws ApiException
     */
    public function getUserInfoByTempCode($code = null)
    {
        $response = $this->http->get($this->getUrl('oauth/getUserInfoByTempCode', array(
            'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret
        )));

        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            $this->openid = $this->result['data']['id'];
            return $this->result;
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 刷新AccessToken续期
     * @param string $refreshToken
     * @return bool
     */
    public function refreshToken($refreshToken)
    {
        // 不支持
        $this->result = $this->http->get($this->getUrl('oauth/refreshToken', array(
            'grant_type' => 'refresh_token',
            'app_id' => $this->appid,
            'app_secret' => $this->appSecret,
            'refresh_token' => $refreshToken,
        )))->json(true);
        return isset($this->result['code']) && 0 == $this->result['code'];
    }

    /**
     * 检验授权凭证AccessToken是否有效
     * @param string $accessToken
     * @return bool
     */
    public function validateAccessToken($accessToken = null)
    {
        try {
            $this->getUserInfo($accessToken);
            return true;
        } catch (ApiException $e) {
            return false;
        }
    }

    /**
     * 获取用户其他系统同一设备登录状态
     * @param $deviceUniqueCode
     * @param $ip
     * @return mixed
     * @throws ApiException
     */
    public function getUserSsoStatusByDevice($deviceUniqueCode, $ip)
    {
        $response = $this->http->get($this->getUrl('oauth/getUserSsoStatusByDevice', array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'ip' => $ip,
            'device_unique_code' => $deviceUniqueCode // 传入设备唯一值
        )));
        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 通过用户主动登录来生成token
     * @param array $loginData 登录数据
     * @param $deviceUniqueCode
     * @param string $ip 设备ip
     * @return mixed
     * @throws ApiException
     */
    public function generateTokenByLogin($loginData, $deviceUniqueCode, $ip)
    {
        $requestData = array_merge($loginData, array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'ip' => $ip,
            'device_unique_code' => $deviceUniqueCode
        ));
        $response = $this->http->get($this->getUrl('oauth/login', $requestData));

        $this->result = $response->json(true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }


    /**
     * 通过access_token 拉取用户信息
     * @param $accessToken
     * @param $deviceUniqueCode
     * @param $ip
     * @return mixed
     * @throws ApiException
     */
    public function getUserInfoByAccessToken($accessToken, $deviceUniqueCode, $ip)
    {
        $requestData = array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'ip' => $ip,
            'device_unique_code' => $deviceUniqueCode,
        );
        $response = $this->http->header("token", $accessToken)->get($this->getUrl('oauth/getInfo', $requestData));

        $this->result = $response->json(true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 刷新AccessToken续期
     * @param string $refreshToken
     * @param $deviceUniqueCode
     * @param $ip
     * @return array|bool
     * @throws ApiException
     */
    public function refreshTokenWithDeviceCode($refreshToken, $deviceUniqueCode, $ip)
    {
        $response = $this->http->get($this->getUrl('oauth/refreshToken', array(
            'grant_type' => 'refresh_token',
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'refresh_token' => $refreshToken,
            'device_unique_code' => $deviceUniqueCode,
            'ip' => $ip,
        )));

        $this->result = $response->json(true);
        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException(isset($this->result['msg']) ? $this->result['msg'] : '', $response->httpCode());
        }
    }

    /**
     * 检验授权凭证AccessToken是否有效 并且返回token的过期时间和关联用户id
     * @param string $accessToken
     * @param $deviceUniqueCode
     * @param $ip
     * @return bool
     */
    public function validateAccessTokenWithDeviceCode($accessToken, $deviceUniqueCode, $ip)
    {
        $requestData = array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'ip' => $ip,
            'access_token' => $accessToken,
            'device_unique_code' => $deviceUniqueCode, // 传入设备唯一值
        );

        $response = $this->http->get($this->getUrl('oauth/checkToken', $requestData));

        $this->result = $response->json(true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            return false;
        }
    }

    /**
     * 取消授权的token
     * @param $accessToken
     * @param $deviceUniqueCode
     * @param $ip
     * @return mixed
     * @throws ApiException
     */
    public function cancelAccessToken($accessToken, $deviceUniqueCode, $ip)
    {
        $requestData = array(
            'app_key' => $this->appid,
            'app_secret' => $this->appSecret,
            'ip' => $ip,
            'access_token' => $accessToken,
            'device_unique_code' => $deviceUniqueCode, // 传入设备唯一值
        );

        $response = $this->http->header('token', $accessToken)->get($this->getUrl('oauth/logout', $requestData));

        $this->result = $response->json(true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            throw new ApiException($this->result['msg'], $this->result['code']);
        }
    }
}
