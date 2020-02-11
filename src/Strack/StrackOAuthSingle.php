<?php

/**
 *  Strak第三方登录认证
 */
class StrackOAuthSingle
{
    private static $data;

    // base url
    private $baseUrl = "";

    /**
     * state值，调用getAuthUrl方法后可以获取到
     * @var string
     */
    public $state;

    //APP ID
    private $appId = "";

    //APP KEY
    private $appSecret = "";

    //回调地址
    private $callbackUrl = "";

    //Authorization Code
    private $code = "";

    //access Token
    private $accessToken = "";

    /**
     * openid，调用相应方法后可以获取到
     * @var string
     */
    public $openid;

    /**
     * 接口调用结果
     * @var array
     */
    public $result;

    public function __construct($appId = null, $appSecret = null, $callbackUrl = null)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->callbackUrl = $callbackUrl;
        //检查用户数据
        if (empty($_SESSION['QC_userData'])) {
            self::$data = array();
        } else {
            self::$data = $_SESSION['QC_userData'];
        }
    }

    /**
     * 因为链接不确定所以需要手动设置
     * @param $url
     */
    public function setBaseUser($url)
    {
        $this->baseUrl = $url . '/';
    }

    /**
     * 获取state值
     * @param string $state
     * @return string
     */
    protected function getState($state = null)
    {
        if(null === $state)
        {
            if(null === $this->state)
            {
                $this->state = md5(\uniqid('', true));
            }
        }
        else
        {
            $this->state = $state;
        }
        return $this->state;
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
            'app_key' => $this->appId,
            'redirect_uri' => null === $callbackUrl ? $this->callbackUrl : $callbackUrl,
            'response_type' => 'code',
            'state' => $this->getState($state),
        );


        $param = http_build_query($option, '', '&');
        $url = $this->baseUrl . "/oauth/authorize?" . $param;

        return $url;
    }


    /**
     * 通过Authorization Code获取Access Token
     * @return mixed
     */
    public function getAccessToken()
    {
        $url = $this->baseUrl."/oauth/getAuthorizeToken";
        $option = array(
            'grant_type' => 'authorization_code',
            'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'app_key' => $this->appId,
            'redirect_uri' => $this->callbackUrl,
            'app_secret' => $this->appSecret,
            'device_unique_code' => isset($_GET['device_unique_code']) ? $_GET['device_unique_code'] : '' // 传入设备唯一值
        );
        $response = $this->postUrl($url, $option);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            return $this->accessToken = $this->result['data']['access_token'];
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
    }

    /**
     * 获取用户资料
     * @param null $accessToken
     * @return array
     */
    public function getUserInfo($accessToken = null)
    {
        $url = $this->baseUrl."/oauth/getUserInfo";
        $param = array(
            'access_token' => null === $accessToken ? $this->accessToken : $accessToken,
        );
        $param = http_build_query($param, '', '&');
        $url = $url . "?" . $param;

        $response =$this->getUrl($url);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            $this->openid = $this->result['data']['id'];
            return $this->result;
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
    }

    /**
     * 获取用户资料通过令牌
     * @param string $code
     * @return array|mixed
     */
    public function getUserInfoByTempCode($code = '')
    {

        $url = $this->baseUrl."/oauth/getUserInfoByTempCode";
        $param = array(
            'code' => isset($code) ? $code : (isset($_GET['code']) ? $_GET['code'] : ''),
            'app_key' => $this->appId,
            'app_secret' => $this->appSecret
        );
        $param = http_build_query($param, '', '&');
        $url = $url . "?" . $param;

        $response =$this->getUrl($url);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            $this->openid = $this->result['data']['id'];
            return $this->result;
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
    }

    /**
     * 刷新AccessToken续期
     * @param string $refreshToken
     * @return bool
     */
    public function refreshToken($refreshToken)
    {
        $url = $this->baseUrl."/oauth/refreshToken";
        $param = array(
            'grant_type' => 'refresh_token',
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'refresh_token' => $refreshToken,
        );
        $param = http_build_query($param, '', '&');
        $url = $url . "?" . $param;

        $response =$this->getUrl($url);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            return isset($this->result['data']['code']) && 0 == $this->result['data']['code'];
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
    }

    /**
     * 获取用户其他系统同一设备登录状态
     * @param $deviceUniqueCode
     * @param null $state
     * @return mixed
     */
    public function getUserOssStatus($deviceUniqueCode, $state = null)
    {
        $url = $this->baseUrl."/oauth/getUserOssStatus";
        $param = array(
            'app_key' => $this->appId,
            'app_secret' => $this->appSecret,
            'state' => $this->getState($state),
            'device_unique_code' => $deviceUniqueCode // 传入设备唯一值
        );
        $param = http_build_query($param, '', '&');
        $url = $url . "?" . $param;

        $response =$this->getUrl($url);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
    }

    /**
     * 注销当前设备用户登录状态
     * @param $deviceUniqueCode
     * @param null $state
     * @return mixed
     */
    public function cancelUserOssStatus($deviceUniqueCode, $state = null)
    {
        $url = $this->baseUrl."/oauth/cancelUserOssStatus";
        $param = array(
            'app_key' => $this->appId,
            'app_secret' => $this->appSecret,
            'state' => $this->getState($state),
            'device_unique_code' => $deviceUniqueCode // 传入设备唯一值
        );
        $param = http_build_query($param, '', '&');
        $url = $url . "?" . $param;

        $response =$this->getUrl($url);

        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        $this->result = json_decode($response, true);

        if ((int)$this->result['code'] === 0) {
            return $this->result['data'];
        } else {
            $errorMsg = isset($this->result['msg']) ? $this->result['msg'] : '';
            exit($errorMsg);
        }
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
        } catch (\Exception $e) {
            return false;
        }
    }

    //CURL GET
    private function getUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * CURL POST
     * @param $url
     * @param $postData
     * @return false|string
     */
    private function postUrl($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}
