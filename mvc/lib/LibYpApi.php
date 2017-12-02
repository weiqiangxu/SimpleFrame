<?php
/*
*SimpleFrame公共接口
*/
class LibYpApi
{
	protected $config = null;

	function __construct($config = null)
	{
		if($config == 'ssl')
		{
			$this->config = mvc::$cfg['YPAPISSL'];
		}
		else
		{
			$this->config = mvc::$cfg['YPAPI'];
		}
	}
	
	/**
	  * @method 发送短信
	  * @http  POST
	  * @param  $url_path           string [必填] action/method
	  * @param  $get_arr            array  [可选] ['name'=>'soul',....]
	  * @param  $post_arr           array  [可选] ['name'=>'soul',....]
	  * @author soul
	  * @copyright 2017/4/13
	  * @return array
	  */
	public function Get($url_path, $get_arr=[], $post_arr=[])
	{
		$url = $this->config['base_url'].$url_path;
		$get_arr['token'] =  $this->config['token'];
		$url = $url.'?'.http_build_query($get_arr);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
		if(!empty($post_arr))
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, ['data'=>json_encode($post_arr)]);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		//echo $return;
		curl_close($ch);
		
		//处理错误提示
		$return = json_decode($html, true);
		if(is_array($return))
		{
			return LibFc::ReturnData($return['status'], $return['data'], $return['code']);
		}
		else
		{
			return LibFc::ReturnData(false, $html);
		}
	}
}