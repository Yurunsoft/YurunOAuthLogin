# YurunOAuthLogin

YurunOAuthLogin是一个使用PHP开发集成登录SDK，测试代码可看test目录。

## 支持的登录平台

- QQ
- 微信
- 微博
- Github

> 后续将不断添加新的平台支持，也欢迎你来提交PR，一起完善！

## [在线文档](http://doc.yurunsoft.com/YurunOAuthLogin "在线文档")

## 安装

在您的composer.json中加入配置：

```json
{
    "require": {
        "yurunsoft/yurun-oauth-login": "1.*"
    }
}
```

## 代码实例

自v1.2起所有方法统一参数调用，如果需要额外参数的可使用对象属性赋值，具体参考test目录下的测试代码。

下面代码以QQ接口举例，完全可以把QQ字样改为其它任意接口字样使用。

### 实例化

```php
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2('appid', 'appkey', 'callbackUrl');
```

### 登录

```php
$url = $qqOAuth->getAuthUrl();
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
header('location:' . $url);
```

### 回调处理

```php
// 获取accessToken
$accessToken = $qqOAuth->getAccessToken($_SESSION['YURUN_QQ_STATE']);

// 调用过getAccessToken方法后也可这么获取
// $accessToken = $qqOAuth->accessToken;
// 这是getAccessToken的api请求返回结果
// $result = $qqOAuth->result;

// 用户资料
$userInfo = $qqOAuth->getUserInfo();

// 这是getAccessToken的api请求返回结果
// $result = $qqOAuth->result;

// 用户唯一标识
$openid = $qqOAuth->openid;
```

### 解决QQ、微信登录只能设置一个回调域名的问题

```php
// 解决只能设置一个回调域名的问题，下面地址需要改成你项目中的地址，可以参考test/QQ/loginAgent.php写法
$qqOAuth->loginAgentUrl = 'http://localhost/test/QQ/loginAgent.php';

$url = $qqOAuth->getAuthUrl();
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
header('location:' . $url);
```

## 特别鸣谢

* [GetWeixinCode](https://github.com/HADB/GetWeixinCode "GetWeixinCode")