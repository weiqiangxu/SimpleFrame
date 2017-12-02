<?php
/**
  * @method 模板
  * @author soul
  * @copyright 2017/7/19
  */
class LibTpl
{
	protected static $setArr = [];
	
	/**
	  * @method 赋值
	  * @author soul
	  */
	public static function Set($key, $value = null) 
	{
		self::$setArr[$key] = $value;
	}
	
	/**
	  * @method 输出模板
	  * @param  $tplFile  模板名称
	  * @param  $tplPath  模板目录[默认mvc::$cfg['PATH_TPL']]
	  * @author soul
	  */
	public static function Put($tplFile='', $tplPath = null)
	{
        /* 当参数为空时，取默认的 action/function.php 模板路径 */
        if(empty($tplFile)){
            if(!empty(mvc::$URL_CLASS_PATH)) $tplFile = mvc::$URL_CLASS_PATH . '/';
            $tplFile .= mvc::$URL_CLASS .'/'. mvc::$URL_METHOD . '.php';
        }
		foreach(self::$setArr as $key=>$value)
		{
			$$key = $value;
		}
		$tplFile = empty($tplPath)? mvc::$cfg['PATH_TPL'].$tplFile: $tplPath.$tplFile;
		include($tplFile);
	}


	/**
	  * @method 得到处理模板后的结果(HTML)
	  * @param  $tplFile  模板名称
	  * @param  $tplPath  模板目录[默认mvc::$cfg['PATH_TPL']]
	  * @author soul
	  */
	public static function Fetch($tplFile='', $tplPath = null)
	{
		ob_start();
		self::Put($tplFile, $tplPath);
		$html = ob_get_contents(); 
		ob_end_clean();
		return $html;
	}

	/**
	  * @name jump(jump)
	  * @remark  页面跳转
	  * @param  $message    string 提示信息
	  * @param  $status       int     状态
	  * @param  $jumpUrl     string      跳转地址
	  * @param  $wait     int      等待时间	 秒 
	  * @author jian
	  * @copyright 2013/4/3
	  */
    protected static function Jump($message,$status=1,$jumpUrl='',$wait=0)
    {
        if($status)
		{
			$wait = empty($wait)? 3: $wait;
			$tpl = 'public/success.php';
		}
		else
		{
			$wait = empty($wait)? 5: $wait;
			$tpl = 'public/error.php';
		}
		$jumpUrl = empty($jumpUrl)? $_SERVER["HTTP_REFERER"]: $jumpUrl;
		self::Set('wait',$wait);
		self::Set('jumpUrl',$jumpUrl);
		self::Set('message',$message);
		$headArr = [
			'title'=>'温馨提醒-SimpleFrame商城',
			'keyword'=>'',	
			'des'=>''
		];
		LibTpl::Set('headArr', $headArr);
		self::put($tpl);
        exit;
    }

	/**
	  * @name Error(Error)
	  * @remark  错误跳转提示
	  * @param  $message    string 提示信息
	  * @param  $jumpUrl     string      跳转地址
	  * @param  $wait     int      等待时间	  
	  * @author jian
	  * @copyright 2013/4/3
	  */
    public static function Error($message,$jumpUrl='',$wait=0)
    {
        self::Jump($message,0,$jumpUrl,$wait);
    }
    
    
	/**
	  * @name Success(Success)
	  * @remark  成功跳转提示
	  * @param  $message    string 提示信息
	  * @param  $jumpUrl     string 跳转地址
	  * @param  $wait     int 等待时间	  
	  * @author jian
	  * @copyright 2013/4/3
	  */
    public static function Success($message,$jumpUrl='',$wait=0)
    {
		self::Jump($message,1,$jumpUrl,$wait);
    }
}