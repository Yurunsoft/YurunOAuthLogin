<?php
namespace Yurun\OAuthLogin\Traits;

trait LoginAgent
{
	/**
	 * 登录代理地址，用于解决只能设置一个回调域名/地址的问题
	 * @var string
	 */
	public $loginAgentUrl;
	
	public function displayLoginAgent()
	{
		$ref = new \ReflectionClass(get_called_class());  
		echo file_get_contents(dirname($ref->getFileName()) . '/loginAgent.html');
	}
}