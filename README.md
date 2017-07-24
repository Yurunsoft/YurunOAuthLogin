# YurunOAuthLogin
PHP封装集成的QQ、微信登录SDK，测试代码可看test目录

## QQ登录

### 实例化

```php
$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2('appid', 'appkey', 'callbackUrl'];
```
### 登录

```php
$url = $qqOAuth->getAuthUrl(
	'callbackUrl', // 回调地址，登录成功后返回该地址
	null, // state 为空自动生成
	null, // scope 只要登录默认为空即可
	null // display 电脑为空，手机为mobile
);
// 记录state
$_SESSION['YURUN_QQ_STATE'] = $qqOAuth->state;
// 跳转登录
header('location:' . $url);
```

### 回调处理

```php
// 获取access_token
$accessToken = $qqOAuth->parseCallback(
	$_SESSION['YURUN_QQ_STATE'], // 保存的state
	null, // state 为null或不传默认从$_GET['state']取
	null, // code 为null或不传默认从$_GET['code']取
	null // redirectUri 为null或不传默认使用实例化传入的callbackUrl
);
// 也可以执行上面语句后通过下面代码获取
$accessToken = $qqOAuth->accessToken;

// 获取openid
$openid = $qqOAuth->getOpenID(
	null // $accessTokenl 为null或不传则自动取上面获取到的
);
// 也可以执行上面语句后通过下面代码获取
$openid = $qqOAuth->openid;

// 用户资料获取
$userInfo = $qqOAuth->getUserInfo(
	null, // $accessToken 为null或不传则自动取上面获取到的
	null // $openid 为null或不传则自动取上面获取到的
);

// 上面每一步获取完后可以通过下面代码获取格式化后的请求数据数组
$result = $qqOAuth->result;
```

## 微信登录

```php
$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2('appid', 'appkey');
```
### 登录

```php
$url = $wxOAuth->getAuthUrl(
	$GLOBALS['oauth_weixin']['callbackUrl'],	// 回调地址，登录成功后返回该地址
	null,										// state 为null或不传自动生成
	null										// scope 只要登录的话，默认为null或不传即可
);
// 记录state
$_SESSION['YURUN_WEIXIN_STATE'] = $wxOAuth->state;
// 跳转登录
header('location:' . $url);
```

### 回调处理

```php
// 获取access_token
$accessToken = $wxOAuth->parseCallback(
	$_SESSION['YURUN_WEIXIN_STATE'], // 保存的state
	null, // state 为null或不传默认从$_GET['state']取
	null, // code 为null或不传默认从$_GET['code']取
));
// 也可以执行上面语句后通过下面代码获取
$accessToken = $wxOAuth->accessToken;
$openid = $wxOAuth->openid;

// 用户信息获取
$userInfo = $wxOAuth->getUserInfo(
	null, // $accessToken 为null或不传则自动取上面获取到的
	null // $openid 普通用户标识，对该公众帐号唯一，为null或不传则使用parseCallback方法调用后的值
);

// 上面每一步获取完后可以通过下面代码获取格式化后的请求数据数组
$result = $wxOAuth->result;
```